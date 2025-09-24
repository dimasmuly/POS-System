<template>
  <div class="h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-4 py-3">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Point of Sale</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-500">Cashier: {{ authStore.user?.name }}</span>
          <button
            @click="showCustomerModal = true"
            class="btn btn-secondary"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            {{ selectedCustomer ? selectedCustomer.name : 'Select Customer' }}
          </button>
        </div>
      </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
      <!-- Product Search & Grid -->
      <div class="flex-1 flex flex-col">
        <!-- Search Bar -->
        <div class="bg-white border-b border-gray-200 p-4">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchQuery"
              @input="searchProducts"
              type="text"
              placeholder="Search products by name, SKU, or barcode..."
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
          </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto bg-gray-50 p-4">
          <div v-if="loading" class="flex items-center justify-center h-full">
            <div class="text-center">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-4 text-gray-500">Loading products...</p>
            </div>
          </div>
          
          <div v-else-if="products.length === 0" class="flex items-center justify-center h-full">
            <div class="text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2"></path>
              </svg>
              <p class="mt-4 text-lg font-medium text-gray-900">No products found</p>
              <p class="mt-2 text-sm text-gray-500">Try adjusting your search query</p>
            </div>
          </div>
          
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div
              v-for="product in products"
              :key="product.id"
              @click="addToCart(product)"
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer"
            >
              <div v-if="product.image" class="aspect-square bg-gray-100 rounded-lg mb-3 overflow-hidden">
                <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
              </div>
              <div v-else class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <h3 class="font-medium text-gray-900 text-sm mb-1 line-clamp-2">{{ product.name }}</h3>
              <p class="text-xs text-gray-500 mb-2">{{ product.sku }}</p>
              <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-green-600">${{ product.price }}</span>
                <span v-if="product.track_inventory" class="text-xs text-gray-500">
                  Stock: {{ product.stock_quantity }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cart Sidebar -->
      <div class="w-96 bg-white border-l border-gray-200 flex flex-col">
        <!-- Cart Header -->
        <div class="border-b border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Cart</h2>
            <button
              v-if="cartItems.length > 0"
              @click="clearCart"
              class="text-sm text-red-600 hover:text-red-700"
            >
              Clear All
            </button>
          </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4">
          <div v-if="cartItems.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <p class="mt-4 text-sm text-gray-500">Your cart is empty</p>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="item in cartItems"
              :key="item.product.id"
              class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ item.product.name }}</p>
                <p class="text-xs text-gray-500">{{ item.product.sku }}</p>
                <p class="text-sm font-medium text-green-600">${{ item.product.price }}</p>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  @click="decreaseQuantity(item)"
                  class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300"
                >
                  -
                </button>
                <span class="text-sm font-medium w-8 text-center">{{ item.quantity }}</span>
                <button
                  @click="increaseQuantity(item)"
                  class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300"
                >
                  +
                </button>
                <button
                  @click="removeFromCart(item)"
                  class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-red-600 hover:bg-red-200 ml-2"
                >
                  ×
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div v-if="cartItems.length > 0" class="border-t border-gray-200 p-4 space-y-4">
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span>Subtotal:</span>
              <span>${{ cartSubtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span>Tax:</span>
              <input
                v-model.number="taxAmount"
                type="number"
                step="0.01"
                min="0"
                class="w-20 px-2 py-1 text-right text-sm border border-gray-300 rounded"
              >
            </div>
            <div class="flex justify-between text-sm">
              <span>Discount:</span>
              <input
                v-model.number="discountAmount"
                type="number"
                step="0.01"
                min="0"
                class="w-20 px-2 py-1 text-right text-sm border border-gray-300 rounded"
              >
            </div>
            <div class="border-t pt-2 flex justify-between text-lg font-bold">
              <span>Total:</span>
              <span>${{ cartTotal.toFixed(2) }}</span>
            </div>
          </div>

          <div class="space-y-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
              <select v-model="paymentMethod" class="form-input">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="digital_wallet">Digital Wallet</option>
                <option value="bank_transfer">Bank Transfer</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
              <input
                v-model.number="paidAmount"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="form-input"
              >
            </div>
            
            <div v-if="paidAmount >= cartTotal" class="text-sm text-green-600">
              Change: ${{ (paidAmount - cartTotal).toFixed(2) }}
            </div>
            
            <button
              @click="processOrder"
              :disabled="processingOrder || paidAmount < cartTotal"
              class="w-full btn btn-primary"
            >
              <span v-if="processingOrder">Processing...</span>
              <span v-else>Complete Sale</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const { $api } = useNuxtApp()
const authStore = useAuthStore()

// Reactive data
const searchQuery = ref('')
const products = ref([])
const cartItems = ref([])
const selectedCustomer = ref(null)
const loading = ref(false)
const processingOrder = ref(false)
const showCustomerModal = ref(false)

// Form data
const taxAmount = ref(0)
const discountAmount = ref(0)
const paymentMethod = ref('cash')
const paidAmount = ref(0)

// Computed
const cartSubtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.product.price * item.quantity), 0)
})

const cartTotal = computed(() => {
  return cartSubtotal.value + taxAmount.value - discountAmount.value
})

// Methods
const searchProducts = async () => {
  if (!searchQuery.value.trim()) {
    products.value = []
    return
  }

  loading.value = true
  try {
    const response = await $api.get(`/products/search/query?query=${encodeURIComponent(searchQuery.value)}`)
    products.value = response.data
  } catch (error) {
    console.error('Failed to search products:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = (product) => {
  const existingItem = cartItems.value.find(item => item.product.id === product.id)
  
  if (existingItem) {
    if (product.track_inventory && existingItem.quantity >= product.stock_quantity) {
      alert('Not enough stock available')
      return
    }
    existingItem.quantity++
  } else {
    if (product.track_inventory && product.stock_quantity <= 0) {
      alert('Product is out of stock')
      return
    }
    cartItems.value.push({
      product: product,
      quantity: 1
    })
  }
  
  // Update paid amount to match total if it's less
  if (paidAmount.value < cartTotal.value) {
    paidAmount.value = cartTotal.value
  }
}

const removeFromCart = (item) => {
  const index = cartItems.value.indexOf(item)
  if (index > -1) {
    cartItems.value.splice(index, 1)
  }
}

const increaseQuantity = (item) => {
  if (item.product.track_inventory && item.quantity >= item.product.stock_quantity) {
    alert('Not enough stock available')
    return
  }
  item.quantity++
  
  if (paidAmount.value < cartTotal.value) {
    paidAmount.value = cartTotal.value
  }
}

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    item.quantity--
  } else {
    removeFromCart(item)
  }
}

const clearCart = () => {
  cartItems.value = []
  taxAmount.value = 0
  discountAmount.value = 0
  paidAmount.value = 0
}

const processOrder = async () => {
  if (cartItems.value.length === 0) {
    alert('Cart is empty')
    return
  }

  if (paidAmount.value < cartTotal.value) {
    alert('Insufficient payment amount')
    return
  }

  processingOrder.value = true

  try {
    const orderData = {
      customer_id: selectedCustomer.value?.id || null,
      items: cartItems.value.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity
      })),
      tax_amount: taxAmount.value,
      discount_amount: discountAmount.value,
      paid_amount: paidAmount.value,
      payment_method: paymentMethod.value
    }

    const response = await $api.post('/orders', orderData)
    
    // Clear cart and reset form
    clearCart()
    selectedCustomer.value = null
    searchQuery.value = ''
    products.value = []
    
    alert(`Order completed successfully! Order #${response.data.order.order_number}`)
  } catch (error) {
    console.error('Failed to process order:', error)
    alert('Failed to process order. Please try again.')
  } finally {
    processingOrder.value = false
  }
}

// Watch for cart total changes to update paid amount
watch(cartTotal, (newTotal) => {
  if (paidAmount.value === 0 || paidAmount.value < newTotal) {
    paidAmount.value = newTotal
  }
})

// Load initial products on mount
onMounted(async () => {
  // You could load featured/popular products here
})
</script>