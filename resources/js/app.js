import './bootstrap';

// Alpine.js
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Cart Store
Alpine.store('cart', {
    items: JSON.parse(localStorage.getItem('cart_items') || '[]'),
    
    get count() {
        return this.items.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0);
    },
    
    get total() {
        return this.items.reduce((sum, item) => {
            const line = (Number(item.price) || 0) * (Number(item.quantity) || 0);
            return sum + (Number.isFinite(line) ? line : 0);
        }, 0);
    },
    
    add(product, quantity = 1, selectedSize = null, selectedToppings = []) {
        // Calculate total price
        let price = parseFloat(product.base_price);
        
        if (selectedSize) {
            price += parseFloat(selectedSize.price_modifier || 0);
        }
        
        selectedToppings.forEach(topping => {
            price += parseFloat(topping.price || 0);
        });
        
        // Create unique key for this combination
        const key = `${product.id}-${selectedSize?.id || 0}-${selectedToppings.map(t => t.id).sort().join(',')}`;
        
        const existingIndex = this.items.findIndex(item => item.key === key);
        
        if (existingIndex > -1) {
            this.items[existingIndex].quantity += quantity;
        } else {
            this.items.push({
                key,
                product_id: product.id,
                name: product.name,
                image: product.image,
                base_price: product.base_price,
                price: price,
                quantity: quantity,
                size: selectedSize,
                toppings: selectedToppings
            });
        }

        this.items = [...this.items];
        this.save();
    },

    addOffer(offer) {
        const key = `offer-${offer.offer_id}`;
        const existingIndex = this.items.findIndex((i) => i.key === key);
        const qty = 1;
        const line = {
            key,
            type: 'offer',
            offer_id: offer.offer_id,
            name: offer.name,
            image: offer.image || null,
            price: parseFloat(offer.price),
            quantity: qty,
            bundle_lines: offer.bundle_lines || [],
        };
        if (existingIndex > -1) {
            this.items[existingIndex].quantity += qty;
        } else {
            this.items.push(line);
        }
        this.items = [...this.items];
        this.save();
    },
    
    update(key, quantity) {
        const index = this.items.findIndex(item => item.key === key);
        if (index > -1) {
            if (quantity <= 0) {
                this.items.splice(index, 1);
            } else {
                this.items[index].quantity = quantity;
            }
            this.items = [...this.items];
            this.save();
        }
    },
    
    remove(key) {
        this.items = this.items.filter(item => item.key !== key);
        this.save();
    },
    
    clear() {
        this.items = [];
        this.save();
    },
    
    save() {
        localStorage.setItem('cart_items', JSON.stringify(this.items));
    }
});

Alpine.start();
