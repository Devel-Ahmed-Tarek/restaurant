<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected array $conversationHistory = [];
    protected array $cartItems = [];
    protected array $customerData = [];
    protected string $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    public function chat(string $message, array $context = []): array
    {
        $this->conversationHistory = $context['history'] ?? [];
        $this->cartItems = $context['cart'] ?? [];
        $this->customerData = $context['customer'] ?? [];
        $this->locale = $context['locale'] ?? 'en';

        $systemPrompt = $this->buildSystemPrompt();
        
        $this->conversationHistory[] = [
            'role' => 'user',
            'content' => $message,
        ];

        try {
            $response = $this->callOpenAI([
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => array_merge(
                    [['role' => 'system', 'content' => $systemPrompt]],
                    $this->conversationHistory
                ),
                'functions' => $this->getAvailableFunctions(),
                'function_call' => 'auto',
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error']);
            }

            $assistantMessage = $response['data']['choices'][0]['message'];
            
            if (isset($assistantMessage['function_call'])) {
                return $this->handleFunctionCall($assistantMessage);
            }

            $this->conversationHistory[] = [
                'role' => 'assistant',
                'content' => $assistantMessage['content'],
            ];

            return [
                'success' => true,
                'message' => $assistantMessage['content'],
                'history' => $this->conversationHistory,
                'cart' => $this->cartItems,
                'customer' => $this->customerData,
                'action' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Chat AI Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            
            $errorMessage = config('app.debug') 
                ? 'Error: ' . $e->getMessage() 
                : $this->getErrorMessage();
            
            return [
                'success' => false,
                'message' => $errorMessage,
                'history' => $this->conversationHistory,
                'cart' => $this->cartItems,
                'customer' => $this->customerData,
                'action' => null,
            ];
        }
    }

    protected function buildSystemPrompt(): string
    {
        $siteName = site_setting('site_name', 'Our Restaurant');
        $currency = currency_symbol();
        $menu = $this->getMenuData();
        $offers = $this->getOffersData();
        
        $langInstructions = "CRITICAL LANGUAGE RULE: You MUST detect the customer's language from their message and respond in that SAME language.
- If customer writes in Arabic (العربية), respond fully in Arabic
- If customer writes in English, respond fully in English  
- If customer writes in German (Deutsch), respond fully in German
- If customer mixes languages, prefer the dominant language they used
- NEVER switch languages mid-conversation unless the customer does
- Use natural, friendly tone in whatever language you're speaking";

        return <<<PROMPT
You are a friendly AI assistant for {$siteName} restaurant. Your job is to help customers:
1. Recommend dishes based on their preferences (spicy/mild, meat type, price range)
2. Show current offers and deals
3. Help them build their order with multiple items
4. Collect order information (name, phone, address)
5. Confirm and place orders

{$langInstructions}

AVAILABLE MENU:
{$menu}

CURRENT OFFERS:
{$offers}

CURRENCY: {$currency}

CURRENT CART:
{$this->formatCart()}

CUSTOMER DATA:
{$this->formatCustomerData()}

IMPORTANT RULES:
- Be friendly, helpful, and concise
- When recommending, consider customer preferences (spicy level, meat preference, budget)
- Always confirm items before adding to cart
- Before placing order, summarize the full order and ask for confirmation
- Collect customer name, phone, and address before placing order
- Use the provided functions to manage cart and orders
- If customer wants non-spicy food, look for items without "spicy" in tags or description
- For chicken lovers, recommend chicken-based dishes
- Always mention prices when recommending items
PROMPT;
    }

    protected function getMenuData(): string
    {
        $categories = Category::active()
            ->ordered()
            ->with(['activeProducts' => function($q) {
                $q->with(['sizes', 'toppings']);
            }])
            ->get();

        $menuText = "";
        foreach ($categories as $category) {
            $menuText .= "\n## {$category->display_name}\n";
            foreach ($category->activeProducts as $product) {
                $tags = $product->tags ? ' [' . implode(', ', $product->tags) . ']' : '';
                $sizes = $product->sizes->isNotEmpty() 
                    ? ' | Sizes: ' . $product->sizes->map(fn($s) => "{$s->display_name} (+{$s->price_modifier})")->implode(', ')
                    : '';
                $toppings = $product->toppings->isNotEmpty()
                    ? ' | Add-ons: ' . $product->toppings->map(fn($t) => "{$t->display_name} (+{$t->price})")->implode(', ')
                    : '';
                
                $menuText .= "- [{$product->id}] {$product->display_name}: {$product->base_price}";
                if ($product->description) {
                    $menuText .= " - {$product->display_description}";
                }
                $menuText .= "{$tags}{$sizes}{$toppings}\n";
            }
        }

        return $menuText ?: "No menu items available.";
    }

    protected function getOffersData(): string
    {
        $offers = Offer::active()
            ->ordered()
            ->with('products')
            ->get();

        if ($offers->isEmpty()) {
            return "No special offers at the moment.";
        }

        $offersText = "";
        foreach ($offers as $offer) {
            $products = $offer->products->map(fn($p) => "{$p->display_name} x{$p->pivot->quantity}")->implode(', ');
            $offersText .= "- [{$offer->id}] {$offer->display_name}: {$offer->bundle_price} (includes: {$products})";
            if ($offer->description) {
                $offersText .= " - {$offer->display_description}";
            }
            $offersText .= "\n";
        }

        return $offersText;
    }

    protected function formatCart(): string
    {
        if (empty($this->cartItems)) {
            return "Empty";
        }

        $cartText = "";
        $total = 0;
        foreach ($this->cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $total += $itemTotal;
            $cartText .= "- {$item['name']} x{$item['quantity']} = {$itemTotal}\n";
        }
        $cartText .= "TOTAL: {$total}";

        return $cartText;
    }

    protected function formatCustomerData(): string
    {
        if (empty($this->customerData)) {
            return "Not collected yet";
        }

        $data = [];
        if (!empty($this->customerData['name'])) $data[] = "Name: {$this->customerData['name']}";
        if (!empty($this->customerData['phone'])) $data[] = "Phone: {$this->customerData['phone']}";
        if (!empty($this->customerData['address'])) $data[] = "Address: {$this->customerData['address']}";

        return empty($data) ? "Not collected yet" : implode(", ", $data);
    }

    protected function getAvailableFunctions(): array
    {
        return [
            [
                'name' => 'search_menu',
                'description' => 'Search for menu items based on criteria like category, price range, tags (spicy, vegetarian, etc.), or keywords',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'Search query (name, description, or tag)',
                        ],
                        'max_price' => [
                            'type' => 'number',
                            'description' => 'Maximum price filter',
                        ],
                        'category' => [
                            'type' => 'string',
                            'description' => 'Category name to filter by',
                        ],
                        'exclude_tags' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Tags to exclude (e.g., "spicy")',
                        ],
                        'include_tags' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => 'Tags to include (e.g., "chicken", "vegetarian")',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'add_to_cart',
                'description' => 'Add a product or offer to the customer cart',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'type' => [
                            'type' => 'string',
                            'enum' => ['product', 'offer'],
                            'description' => 'Type of item to add',
                        ],
                        'id' => [
                            'type' => 'integer',
                            'description' => 'Product or Offer ID',
                        ],
                        'quantity' => [
                            'type' => 'integer',
                            'description' => 'Quantity to add',
                            'default' => 1,
                        ],
                        'size_id' => [
                            'type' => 'integer',
                            'description' => 'Size ID for products with sizes',
                        ],
                        'topping_ids' => [
                            'type' => 'array',
                            'items' => ['type' => 'integer'],
                            'description' => 'Array of topping IDs to add',
                        ],
                    ],
                    'required' => ['type', 'id'],
                ],
            ],
            [
                'name' => 'remove_from_cart',
                'description' => 'Remove an item from the cart',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'index' => [
                            'type' => 'integer',
                            'description' => 'Index of the item in cart (0-based)',
                        ],
                    ],
                    'required' => ['index'],
                ],
            ],
            [
                'name' => 'update_customer_info',
                'description' => 'Update customer information for the order',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                            'description' => 'Customer full name',
                        ],
                        'phone' => [
                            'type' => 'string',
                            'description' => 'Customer phone number',
                        ],
                        'address' => [
                            'type' => 'string',
                            'description' => 'Delivery address',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'get_offers',
                'description' => 'Get all current active offers/deals',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object)[],
                ],
            ],
            [
                'name' => 'place_order',
                'description' => 'Place the order after customer confirmation. Only call this when cart has items and customer info is complete.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'notes' => [
                            'type' => 'string',
                            'description' => 'Any special instructions for the order',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'get_cart_summary',
                'description' => 'Get the current cart contents and total',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object)[],
                ],
            ],
        ];
    }

    protected function handleFunctionCall(array $assistantMessage): array
    {
        $functionCall = $assistantMessage['function_call'];
        $functionName = $functionCall['name'];
        $arguments = json_decode($functionCall['arguments'], true) ?? [];

        $result = match($functionName) {
            'search_menu' => $this->searchMenu($arguments),
            'add_to_cart' => $this->addToCart($arguments),
            'remove_from_cart' => $this->removeFromCart($arguments),
            'update_customer_info' => $this->updateCustomerInfo($arguments),
            'get_offers' => $this->getOffers(),
            'place_order' => $this->placeOrder($arguments),
            'get_cart_summary' => $this->getCartSummary(),
            default => ['error' => 'Unknown function'],
        };

        $this->conversationHistory[] = [
            'role' => 'assistant',
            'content' => null,
            'function_call' => [
                'name' => $functionName,
                'arguments' => $functionCall['arguments'],
            ],
        ];

        $this->conversationHistory[] = [
            'role' => 'function',
            'name' => $functionName,
            'content' => json_encode($result),
        ];

        $followUpResponse = $this->callOpenAI([
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => array_merge(
                [['role' => 'system', 'content' => $this->buildSystemPrompt()]],
                $this->conversationHistory
            ),
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        $finalMessage = $followUpResponse['data']['choices'][0]['message']['content'] ?? 'Sorry, I could not process that.';

        $this->conversationHistory[] = [
            'role' => 'assistant',
            'content' => $finalMessage,
        ];

        $action = null;
        if ($functionName === 'place_order' && isset($result['order'])) {
            $action = [
                'type' => 'order_placed',
                'order' => $result['order'],
            ];
        } elseif ($functionName === 'add_to_cart' && isset($result['added'])) {
            $action = [
                'type' => 'cart_updated',
                'cart' => $this->cartItems,
            ];
        }

        return [
            'success' => true,
            'message' => $finalMessage,
            'history' => $this->conversationHistory,
            'cart' => $this->cartItems,
            'customer' => $this->customerData,
            'action' => $action,
        ];
    }

    protected function searchMenu(array $args): array
    {
        $query = Product::available()->with(['category', 'sizes', 'toppings']);

        if (!empty($args['query'])) {
            $search = $args['query'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_de', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('description_de', 'like', "%{$search}%");
            });
        }

        if (!empty($args['max_price'])) {
            $query->where('base_price', '<=', $args['max_price']);
        }

        if (!empty($args['category'])) {
            $query->whereHas('category', function($q) use ($args) {
                $q->where('name', 'like', "%{$args['category']}%")
                  ->orWhere('name_de', 'like', "%{$args['category']}%");
            });
        }

        $products = $query->limit(10)->get();

        if (!empty($args['exclude_tags'])) {
            $products = $products->filter(function($p) use ($args) {
                $productTags = array_map('strtolower', $p->tags ?? []);
                foreach ($args['exclude_tags'] as $tag) {
                    if (in_array(strtolower($tag), $productTags)) {
                        return false;
                    }
                }
                return true;
            });
        }

        if (!empty($args['include_tags'])) {
            $products = $products->filter(function($p) use ($args) {
                $productTags = array_map('strtolower', $p->tags ?? []);
                foreach ($args['include_tags'] as $tag) {
                    if (in_array(strtolower($tag), $productTags)) {
                        return true;
                    }
                }
                return false;
            });
        }

        return [
            'products' => $products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->display_name,
                'description' => $p->display_description,
                'price' => $p->base_price,
                'category' => $p->category?->display_name,
                'tags' => $p->tags,
                'sizes' => $p->sizes->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->display_name,
                    'price_modifier' => $s->price_modifier,
                ]),
                'toppings' => $p->toppings->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->display_name,
                    'price' => $t->price,
                ]),
            ])->values(),
        ];
    }

    protected function addToCart(array $args): array
    {
        $type = $args['type'];
        $id = $args['id'];
        $quantity = $args['quantity'] ?? 1;

        if ($type === 'offer') {
            $offer = Offer::active()->with('products')->find($id);
            if (!$offer) {
                return ['error' => 'Offer not found or inactive'];
            }

            $this->cartItems[] = [
                'type' => 'offer',
                'offer_id' => $offer->id,
                'name' => $offer->display_name,
                'price' => (float) $offer->bundle_price,
                'quantity' => $quantity,
            ];

            return [
                'added' => true,
                'item' => $offer->display_name,
                'price' => $offer->bundle_price,
                'quantity' => $quantity,
            ];
        } else {
            $product = Product::available()->with(['sizes', 'toppings'])->find($id);
            if (!$product) {
                return ['error' => 'Product not found or unavailable'];
            }

            $price = (float) $product->base_price;
            $sizeName = null;
            $sizeId = null;
            $toppingsList = [];
            $toppingIds = [];

            if (!empty($args['size_id'])) {
                $size = $product->sizes->find($args['size_id']);
                if ($size) {
                    $price += (float) $size->price_modifier;
                    $sizeName = $size->display_name;
                    $sizeId = $size->id;
                }
            }

            if (!empty($args['topping_ids'])) {
                foreach ($args['topping_ids'] as $toppingId) {
                    $topping = $product->toppings->find($toppingId);
                    if ($topping) {
                        $price += (float) $topping->price;
                        $toppingsList[] = $topping->display_name;
                        $toppingIds[] = ['id' => $topping->id];
                    }
                }
            }

            $itemName = $product->display_name;
            if ($sizeName) $itemName .= " ({$sizeName})";
            if (!empty($toppingsList)) $itemName .= " + " . implode(', ', $toppingsList);

            $this->cartItems[] = [
                'type' => 'product',
                'product_id' => $product->id,
                'name' => $itemName,
                'price' => $price,
                'quantity' => $quantity,
                'size_id' => $sizeId,
                'toppings' => $toppingIds,
            ];

            return [
                'added' => true,
                'item' => $itemName,
                'price' => $price,
                'quantity' => $quantity,
            ];
        }
    }

    protected function removeFromCart(array $args): array
    {
        $index = $args['index'];
        
        if (!isset($this->cartItems[$index])) {
            return ['error' => 'Item not found in cart'];
        }

        $removed = $this->cartItems[$index];
        array_splice($this->cartItems, $index, 1);

        return [
            'removed' => true,
            'item' => $removed['name'],
        ];
    }

    protected function updateCustomerInfo(array $args): array
    {
        if (isset($args['name'])) {
            $this->customerData['name'] = $args['name'];
        }
        if (isset($args['phone'])) {
            $this->customerData['phone'] = $args['phone'];
        }
        if (isset($args['address'])) {
            $this->customerData['address'] = $args['address'];
        }

        return [
            'updated' => true,
            'customer' => $this->customerData,
        ];
    }

    protected function getOffers(): array
    {
        $offers = Offer::active()->ordered()->with('products')->get();

        return [
            'offers' => $offers->map(fn($o) => [
                'id' => $o->id,
                'name' => $o->display_name,
                'description' => $o->display_description,
                'price' => $o->bundle_price,
                'original_price' => $o->reference_total,
                'products' => $o->products->map(fn($p) => [
                    'name' => $p->display_name,
                    'quantity' => $p->pivot->quantity,
                ]),
            ]),
        ];
    }

    protected function getCartSummary(): array
    {
        $total = 0;
        $items = [];

        foreach ($this->cartItems as $index => $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $total += $itemTotal;
            $items[] = [
                'index' => $index,
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total' => $itemTotal,
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
            'item_count' => count($this->cartItems),
        ];
    }

    protected function placeOrder(array $args): array
    {
        if (empty($this->cartItems)) {
            return ['error' => 'Cart is empty'];
        }

        if (empty($this->customerData['name']) || empty($this->customerData['phone']) || empty($this->customerData['address'])) {
            $missing = [];
            if (empty($this->customerData['name'])) $missing[] = 'name';
            if (empty($this->customerData['phone'])) $missing[] = 'phone';
            if (empty($this->customerData['address'])) $missing[] = 'address';
            return ['error' => 'Missing customer information: ' . implode(', ', $missing)];
        }

        $orderItems = [];
        foreach ($this->cartItems as $item) {
            if ($item['type'] === 'offer') {
                $orderItems[] = [
                    'offer_id' => $item['offer_id'],
                    'quantity' => $item['quantity'],
                ];
            } else {
                $orderItem = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ];
                if (!empty($item['size_id'])) {
                    $orderItem['size_id'] = $item['size_id'];
                }
                if (!empty($item['toppings'])) {
                    $orderItem['toppings'] = $item['toppings'];
                }
                $orderItems[] = $orderItem;
            }
        }

        $request = new \Illuminate\Http\Request([
            'customer_name' => $this->customerData['name'],
            'customer_phone' => $this->customerData['phone'],
            'customer_address' => $this->customerData['address'],
            'notes' => $args['notes'] ?? 'Order placed via AI Chat',
            'items' => $orderItems,
        ]);

        $orderController = new \App\Http\Controllers\OrderController();
        $response = $orderController->store($request);
        $data = json_decode($response->getContent(), true);

        if ($data['success'] ?? false) {
            $this->cartItems = [];
            return [
                'success' => true,
                'order' => $data['order'],
            ];
        }

        return ['error' => $data['message'] ?? 'Failed to place order'];
    }

    protected function callOpenAI(array $payload): array
    {
        $apiKey = config('services.openai.api_key') ?: env('OPENAI_API_KEY');
        
        if (empty($apiKey)) {
            return [
                'success' => false,
                'error' => 'OpenAI API key is not configured',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            $error = $response->json();
            return [
                'success' => false,
                'error' => $error['error']['message'] ?? 'OpenAI API error: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to connect to OpenAI: ' . $e->getMessage(),
            ];
        }
    }

    protected function getErrorMessage(): string
    {
        return "Sorry, something went wrong. Please try again. | عذراً، حدث خطأ. يرجى المحاولة مرة أخرى. | Entschuldigung, ein Fehler ist aufgetreten.";
    }
}
