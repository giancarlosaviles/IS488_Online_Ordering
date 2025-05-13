// confirmation.js

document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Elements ---
    const etaDisplay = document.getElementById('eta');
    const userNameDisplay = document.getElementById('user-name-display'); // For guest/student name
    const pickupTimeDisplay = document.getElementById('pickup-time-display');
    const paymentMethodDisplay = document.getElementById('payment-method-display');
    const orderedItemsList = document.getElementById('ordered-items-list');
    const subtotalDisplay = document.getElementById('subtotal-display');
    const taxDisplay = document.getElementById('tax-display');
    const totalDisplay = document.getElementById('total-display');
    const restaurantInfoDisplay = document.getElementById('restaurant-info'); // For restaurant name
    
    // Create an element to display the Order ID
    const orderIdDisplayElement = document.createElement('p');


    /**
     * Loads order data from localStorage and populates the confirmation page.
     */
    function loadOrderData() {
        const orderConfirmationString = localStorage.getItem('currentOrderConfirmation');

        // Prepare and insert the Order ID display element
        const leadText = document.querySelector('.confirmation-container .lead-text');
        if (leadText && leadText.parentNode) {
            // Insert orderIdDisplayElement after the leadText
            leadText.parentNode.insertBefore(orderIdDisplayElement, leadText.nextSibling);
            orderIdDisplayElement.classList.add('my-3', 'text-center', 'text-lg', 'font-semibold'); // Added font-semibold
        }


        if (!orderConfirmationString) {
            // Handle case where no order data is found
            orderedItemsList.innerHTML = '<li>No order confirmation details found. Please start a new order.</li>';
            etaDisplay.textContent = 'N/A';
            if(userNameDisplay) userNameDisplay.textContent = "Valued Customer";
            orderIdDisplayElement.innerHTML = `Order ID: <span class="highlight font-bold text-red-600">Not Available</span>`;
            // Potentially redirect or show a more prominent error
            return;
        }

        try {
            const confirmData = JSON.parse(orderConfirmationString);

            // Populate Order ID
            orderIdDisplayElement.innerHTML = `Order ID: <strong class="highlight">${confirmData.order_id || 'N/A'}</strong>`;
            
            // Populate general order information
            if (confirmData.estimatedPrepTime) {
                etaDisplay.textContent = confirmData.estimatedPrepTime;
            }

            if (userNameDisplay && confirmData.customerName) {
                userNameDisplay.textContent = confirmData.customerName;
            } else if (userNameDisplay) {
                userNameDisplay.textContent = "Valued Customer"; // Default if name not passed
            }

            pickupTimeDisplay.textContent = confirmData.pickupTime || 'Not specified';
            paymentMethodDisplay.textContent = confirmData.paymentMethod || 'Not specified';
            
            if(confirmData.restaurant && restaurantInfoDisplay) {
                restaurantInfoDisplay.innerHTML = `Pickup at: <span class="font-bold">${confirmData.restaurant}</span>`;
            }


            // Populate ordered items
            orderedItemsList.innerHTML = ''; // Clear loading/default message
            if (confirmData.items && confirmData.items.length > 0) {
                let calculatedSubtotal = 0;
                confirmData.items.forEach(item => {
                    const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                    calculatedSubtotal += itemTotal;
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `
                        <span class="item-name">${item.name} (x${item.quantity})</span>
                        <span class="item-qty-price">$${itemTotal.toFixed(2)}</span>
                    `;
                    orderedItemsList.appendChild(listItem);
                });
                
                // Display calculated subtotal and tax
                const taxRate = 0.08; // Ensure this matches the rate used on the order page
                const calculatedTax = calculatedSubtotal * taxRate;
                if (subtotalDisplay) subtotalDisplay.textContent = `$${calculatedSubtotal.toFixed(2)}`;
                if (taxDisplay) taxDisplay.textContent = `$${calculatedTax.toFixed(2)}`;

            } else {
                orderedItemsList.innerHTML = '<li>No items in this order.</li>';
            }

            // Populate total (use the total from confirmData as it's authoritative)
            if (confirmData.total !== undefined) {
                totalDisplay.textContent = `$${parseFloat(confirmData.total).toFixed(2)}`;
            } else {
                // Fallback if total is not in confirmData for some reason
                const finalTotal = calculatedSubtotal + calculatedTax;
                totalDisplay.textContent = `$${finalTotal.toFixed(2)}`;
            }


            // Clear the stored order data after displaying it
            localStorage.removeItem('currentOrderConfirmation');

        } catch (e) {
            console.error("Error parsing order confirmation data from localStorage: ", e);
            orderedItemsList.innerHTML = '<li>Could not load order details due to a data error.</li>';
            if(userNameDisplay) userNameDisplay.textContent = "Error";
            orderIdDisplayElement.innerHTML = `Order ID: <span class="highlight font-bold text-red-600">Error Loading</span>`;
        }
    }

    // --- Initial Load ---
    loadOrderData();
});
