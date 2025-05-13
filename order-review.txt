// order-review.js

document.addEventListener('DOMContentLoaded', () => {
    // --- Global State & Configuration ---
    const TAX_RATE = 0.08;
    let orderItems = [
        // Ensure these IDs match item_id in your Menu_Items table for "Edit" link to be meaningful
        // and for the PHP script to correctly reference them.
        { id: 1, name: 'Spicy Chicken Sandwich', price: 6.99, quantity: 1, customization: null },
        { id: 2, name: 'Waffle Fries', price: 2.49, quantity: 2, customization: null }
        // In a real application, these items would be loaded from localStorage or
        // passed from a previous (menu) page.
    ];

    // --- DOM Elements ---
    const orderItemsContainer = document.getElementById('order-items-container');
    const subtotalAmountEl = document.getElementById('subtotal-amount');
    const taxAmountEl = document.getElementById('tax-amount');
    const totalAmountEl = document.getElementById('total-amount');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const pickupTimeSelect = document.getElementById('pickup-time');

    // User Type Elements
    const userTypeRadios = document.querySelectorAll('input[name="user-type"]');
    const guestDetailsContainer = document.getElementById('guest-details-container');
    const guestNameInput = document.getElementById('guest-name');
    const guestEmailInput = document.getElementById('guest-email');
    const greetingMessageEl = document.getElementById('greeting-message');
    const studentNameForGreeting = "Retriever"; // Placeholder for logged-in student name

    // Payment Method Elements
    const paymentOptionRadios = document.querySelectorAll('input[name="payment-method-option"]');
    const cardDetailsContainer = document.getElementById('card-details-container');
    const cardNumberInput = document.getElementById('card-number');
    const mealPlanDetailsContainer = document.getElementById('meal-plan-details-container');
    const mealPlanNumberInput = document.getElementById('meal-plan-number');
    const payInStoreMessageContainer = document.getElementById('pay-in-store-message-container');

    // --- Core Functions ---

    /**
     * Renders all order items to the DOM with edit, quantity, and remove controls.
     */
    function renderOrderItems() {
        orderItemsContainer.innerHTML = ''; // Clear existing items
        if (orderItems.length === 0) {
            orderItemsContainer.innerHTML = '<p class="text-gray-500 italic">Your cart is empty.</p>';
            updateTotals(); // Ensure totals are zeroed out
            return;
        }
        orderItems.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.classList.add('item'); // Styled in HTML <style> block

            itemElement.innerHTML = `
                <div class="item-details">
                    <p class="font-semibold text-gray-800">${item.name}</p>
                    <p class="text-sm text-gray-600">$${item.price.toFixed(2)} each</p>
                    <a href="menu.html#item-${item.id}" class="edit-link">Edit</a>
                </div>
                <div class="item-controls">
                    <div class="counter">
                        <button data-id="${item.id}" class="quantity-decrease" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                        <span class="quantity mx-2 text-gray-700 font-medium">${item.quantity}</span>
                        <button data-id="${item.id}" class="quantity-increase">+</button>
                    </div>
                    <button data-id="${item.id}" class="remove-btn">Remove</button>
                </div>
            `;
            orderItemsContainer.appendChild(itemElement);
        });
        addEventListenersToItemButtons();
        updateTotals();
    }

    /**
     * Adds event listeners to quantity and remove buttons for order items.
     */
    function addEventListenersToItemButtons() {
        document.querySelectorAll('.quantity-decrease').forEach(button => {
            button.addEventListener('click', handleQuantityChange);
        });
        document.querySelectorAll('.quantity-increase').forEach(button => {
            button.addEventListener('click', handleQuantityChange);
        });
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', handleRemoveItem);
        });
    }

    /**
     * Handles quantity increase or decrease for an order item.
     */
    function handleQuantityChange(event) {
        const itemId = parseInt(event.target.dataset.id);
        const item = orderItems.find(i => i.id === itemId);
        if (item) {
            if (event.target.classList.contains('quantity-increase')) {
                item.quantity++;
            } else if (event.target.classList.contains('quantity-decrease')) {
                if (item.quantity > 1) { // Prevent quantity from going below 1 with this button
                    item.quantity--;
                }
            }
            renderOrderItems(); // Re-render to update UI (including button disabled state and totals)
        }
    }

    /**
     * Handles item removal from the order.
     */
    function handleRemoveItem(event) {
        const itemId = parseInt(event.target.dataset.id);
        orderItems = orderItems.filter(item => item.id !== itemId);
        renderOrderItems(); // Re-render to update UI
    }

    /**
     * Calculates and updates the subtotal, tax, and total amounts in the DOM.
     */
    function updateTotals() {
        const subtotal = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const tax = subtotal * TAX_RATE;
        const total = subtotal + tax;
        subtotalAmountEl.textContent = `$${subtotal.toFixed(2)}`;
        taxAmountEl.textContent = `$${tax.toFixed(2)}`;
        totalAmountEl.textContent = `$${total.toFixed(2)}`;
    }

    /**
     * Handles changes in the selected payment method.
     * Shows/hides the relevant input fields.
     */
    function handlePaymentMethodChange() {
        const selectedValue = document.querySelector('input[name="payment-method-option"]:checked')?.value;

        // Hide all details containers first
        cardDetailsContainer.classList.add('hidden');
        mealPlanDetailsContainer.classList.add('hidden');
        payInStoreMessageContainer.classList.add('hidden');

        // Remove 'selected' class from all payment options
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));

        // Show the relevant one and add 'selected' class
        const selectedOptionElement = document.querySelector('input[name="payment-method-option"]:checked');
        if (selectedOptionElement && selectedOptionElement.closest('.payment-option')) {
            selectedOptionElement.closest('.payment-option').classList.add('selected');
        }


        if (selectedValue === 'Credit/Debit Card') {
            cardDetailsContainer.classList.remove('hidden');
        } else if (selectedValue === 'Meal Plan') {
            mealPlanDetailsContainer.classList.remove('hidden');
        } else if (selectedValue === 'Pay in Store') {
            payInStoreMessageContainer.classList.remove('hidden');
        }
    }

    /**
     * Handles changes in user type selection (Guest/Student).
     */
    function handleUserTypeChange() {
        const selectedUserType = document.querySelector('input[name="user-type"]:checked').value;
        if (selectedUserType === 'guest') {
            guestDetailsContainer.classList.remove('hidden');
            greetingMessageEl.textContent = 'Hi, Guest!';
        } else { // 'student'
            guestDetailsContainer.classList.add('hidden');
            // In a real app, student name would come from session/login data
            greetingMessageEl.textContent = `Hi, ${studentNameForGreeting}!`;
        }
    }

    /**
     * Handles the place order process.
     * Sends data to PHP script via fetch.
     */
    async function handlePlaceOrder() {
        placeOrderBtn.disabled = true;
        placeOrderBtn.textContent = 'Placing Order...';

        const selectedUserType = document.querySelector('input[name="user-type"]:checked').value;
        let guestName = null;
        let guestEmail = null;

        if (selectedUserType === 'guest') {
            guestName = guestNameInput.value.trim();
            guestEmail = guestEmailInput.value.trim();
            if (!guestName) {
                alert("Please enter your name for the guest order.");
                guestNameInput.focus();
                placeOrderBtn.disabled = false;
                placeOrderBtn.textContent = 'Place Order';
                return;
            }
            if (guestEmail && !/^\S+@\S+\.\S+$/.test(guestEmail)) { // Simple email validation
                 alert("Please enter a valid email address for confirmation.");
                guestEmailInput.focus();
                placeOrderBtn.disabled = false;
                placeOrderBtn.textContent = 'Place Order';
                return;
            }
        }

        const selectedPickupTime = pickupTimeSelect.value;
        const selectedPaymentOption = document.querySelector('input[name="payment-method-option"]:checked');
        let paymentMethodValue = '';
        let paymentInstrumentValue = '';

        if (selectedPaymentOption) {
            paymentMethodValue = selectedPaymentOption.value;
            if (paymentMethodValue === 'Credit/Debit Card') {
                paymentInstrumentValue = cardNumberInput.value.trim();
                if (!paymentInstrumentValue) {
                    alert("Please enter your card number.");
                    cardNumberInput.focus();
                    placeOrderBtn.disabled = false; placeOrderBtn.textContent = 'Place Order'; return;
                }
            } else if (paymentMethodValue === 'Meal Plan') {
                paymentInstrumentValue = mealPlanNumberInput.value.trim();
                if (!paymentInstrumentValue) {
                    alert("Please enter your meal plan number.");
                    mealPlanNumberInput.focus();
                    placeOrderBtn.disabled = false; placeOrderBtn.textContent = 'Place Order'; return;
                }
            }
            // No specific instrument needed for "Pay in Store"
        } else {
            alert("Please select a payment method.");
            placeOrderBtn.disabled = false; placeOrderBtn.textContent = 'Place Order'; return;
        }

        if (orderItems.length === 0) {
            alert("Your cart is empty. Please add items to your order.");
            placeOrderBtn.disabled = false; placeOrderBtn.textContent = 'Place Order'; return;
        }
        if (!selectedPickupTime) {
            alert("Please select a pickup time.");
            pickupTimeSelect.focus();
            placeOrderBtn.disabled = false; placeOrderBtn.textContent = 'Place Order'; return;
        }

        const orderDataForPHP = {
            userType: selectedUserType,
            guestDetails: selectedUserType === 'guest' ? { name: guestName, email: guestEmail } : null,
            items: orderItems.map(item => ({
                id: item.id, // This is the Menu_Items.item_id
                quantity: item.quantity,
                price: item.price, // Price per unit for reference
                name: item.name, // Name for reference
                customization: item.customization
            })),
            subtotal: parseFloat(subtotalAmountEl.textContent.replace('$', '')),
            tax: parseFloat(taxAmountEl.textContent.replace('$', '')),
            total: parseFloat(totalAmountEl.textContent.replace('$', '')),
            pickupTime: selectedPickupTime,
            paymentMethod: paymentMethodValue,
            paymentInstrument: paymentInstrumentValue,
            restaurant: "Chick-fil-A @ The Commons", // This could be dynamic
            estimatedPrepTime: "15-20 minutes", // This could be dynamic
            orderTimestamp: new Date().toISOString() // Client-side timestamp
        };

        try {
            const response = await fetch('place_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', },
                body: JSON.stringify(orderDataForPHP),
            });
            if (!response.ok) {
                const errorText = await response.text(); // Get error text from server
                throw new Error(`Network response was not ok: ${response.status} ${response.statusText}. Server says: ${errorText}`);
            }
            const result = await response.json();
            if (result.success) {
                localStorage.setItem('currentOrderConfirmation', JSON.stringify({
                    order_id: result.order_id,
                    total: result.total,
                    pickupTime: result.pickup_time,
                    items: orderDataForPHP.items, // Pass items for display on confirmation
                    restaurant: orderDataForPHP.restaurant,
                    estimatedPrepTime: orderDataForPHP.estimatedPrepTime,
                    paymentMethod: orderDataForPHP.paymentMethod,
                    customerName: result.customerName || (selectedUserType === 'guest' ? guestName : studentNameForGreeting)
                }));
                window.location.href = 'confirmation.html';
            } else {
                alert(`Order failed: ${result.message}`);
                console.error('Order placement failed:', result);
            }
        } catch (error) {
            console.error("Error sending order to server:", error);
            alert("There was a problem connecting to the server to place your order. Please check your network connection and try again. Details: " + error.message);
        } finally {
            placeOrderBtn.disabled = false;
            placeOrderBtn.textContent = 'Place Order';
        }
    }


    // --- Event Listeners ---
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', handlePlaceOrder);
    }
    paymentOptionRadios.forEach(radio => {
        radio.addEventListener('change', handlePaymentMethodChange);
    });
    document.querySelectorAll('.payment-option').forEach(box => {
        box.addEventListener('click', (event) => {
            // Prevent clicks on input/label inside the box from triggering this twice
            if (event.target.name !== "payment-method-option" && event.target.tagName !== 'LABEL') {
                 const radio = box.querySelector('input[type="radio"]');
                 if (radio && !radio.checked) { // Only proceed if radio is not already checked
                    radio.checked = true;
                    // Manually trigger change event for consistent behavior
                    const changeEvent = new Event('change', { bubbles: true });
                    radio.dispatchEvent(changeEvent);
                 }
            }
        });
    });

    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', handleUserTypeChange);
    });


    // --- Initial Render & Setup ---
    renderOrderItems();
    handlePaymentMethodChange(); // Call on load to set initial state for payment inputs
    handleUserTypeChange(); // Call on load to set initial state for guest/student fields
});
