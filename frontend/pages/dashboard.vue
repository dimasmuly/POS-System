<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-gray-600">Welcome back, {{ authStore.user?.name }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-500 truncate">Today's Sales</p>
              <p class="text-3xl font-bold text-green-600">${{ todayStats.total_sales || 0 }}</p>
            </div>
            <div class="ml-3">
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-500 truncate">Today's Orders</p>
              <p class="text-3xl font-bold text-blue-600">{{ todayStats.total_orders || 0 }}</p>
            </div>
            <div class="ml-3">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-500 truncate">Low Stock Items</p>
              <p class="text-3xl font-bold text-yellow-600">{{ lowStockCount }}</p>
            </div>
            <div class="ml-3">
              <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-500 truncate">Average Order</p>
              <p class="text-3xl font-bold text-purple-600">${{ todayStats.average_order_value || 0 }}</p>
            </div>
            <div class="ml-3">
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Orders -->
      <div class="card">
        <div class="card-header">
          <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
        </div>
        <div class="card-body">
          <div v-if="recentOrders.length === 0" class="text-center py-4">
            <p class="text-gray-500">No recent orders</p>
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="order in recentOrders"
              :key="order.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div>
                <p class="font-medium">{{ order.order_number }}</p>
                <p class="text-sm text-gray-500">
                  {{ order.customer?.name || 'Walk-in Customer' }}
                </p>
              </div>
              <div class="text-right">
                <p class="font-medium">${{ order.total_amount }}</p>
                <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
              </div>
            </div>
          </div>
          <div class="mt-4">
            <NuxtLink to="/orders" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
              View all orders →
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card">
        <div class="card-header">
          <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="card-body">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <NuxtLink
              to="/pos"
              class="flex items-center justify-center p-6 bg-green-50 border-2 border-dashed border-green-200 rounded-lg hover:border-green-300 transition-colors"
            >
              <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <p class="mt-2 text-sm font-medium text-green-600">New Sale</p>
              </div>
            </NuxtLink>

            <NuxtLink
              to="/products"
              class="flex items-center justify-center p-6 bg-blue-50 border-2 border-dashed border-blue-200 rounded-lg hover:border-blue-300 transition-colors"
            >
              <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <p class="mt-2 text-sm font-medium text-blue-600">Manage Products</p>
              </div>
            </NuxtLink>

            <NuxtLink
              to="/customers"
              class="flex items-center justify-center p-6 bg-purple-50 border-2 border-dashed border-purple-200 rounded-lg hover:border-purple-300 transition-colors"
            >
              <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <p class="mt-2 text-sm font-medium text-purple-600">Manage Customers</p>
              </div>
            </NuxtLink>

            <NuxtLink
              to="/reports"
              class="flex items-center justify-center p-6 bg-yellow-50 border-2 border-dashed border-yellow-200 rounded-lg hover:border-yellow-300 transition-colors"
            >
              <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="mt-2 text-sm font-medium text-yellow-600">View Reports</p>
              </div>
            </NuxtLink>
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

const todayStats = ref({})
const recentOrders = ref([])
const lowStockCount = ref(0)

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

// Fetch dashboard data
const fetchDashboardData = async () => {
  try {
    const [statsResponse, ordersResponse, lowStockResponse] = await Promise.all([
      $api.get('/sales/summary?period=today'),
      $api.get('/orders?per_page=5'),
      $api.get('/products/inventory/low-stock')
    ])

    todayStats.value = statsResponse.data
    recentOrders.value = ordersResponse.data.data || []
    lowStockCount.value = lowStockResponse.data.length
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>