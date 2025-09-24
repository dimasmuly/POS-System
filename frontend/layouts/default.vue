<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex">
            <div class="flex flex-shrink-0 items-center">
              <NuxtLink to="/dashboard" class="text-2xl font-bold text-blue-600">
                POS System
              </NuxtLink>
            </div>
            <div class="hidden sm:-my-px sm:ml-10 sm:flex sm:space-x-8">
              <NuxtLink
                to="/dashboard"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 border-b-2"
                :class="$route.path === '/dashboard' ? 'border-blue-500' : 'border-transparent hover:border-gray-300'"
              >
                Dashboard
              </NuxtLink>
              <NuxtLink
                to="/pos"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2"
                :class="$route.path === '/pos' ? 'border-blue-500 text-gray-900' : 'border-transparent hover:border-gray-300 hover:text-gray-700'"
              >
                POS
              </NuxtLink>
              <NuxtLink
                to="/products"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2"
                :class="$route.path.startsWith('/products') ? 'border-blue-500 text-gray-900' : 'border-transparent hover:border-gray-300 hover:text-gray-700'"
              >
                Products
              </NuxtLink>
              <NuxtLink
                to="/customers"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2"
                :class="$route.path.startsWith('/customers') ? 'border-blue-500 text-gray-900' : 'border-transparent hover:border-gray-300 hover:text-gray-700'"
              >
                Customers
              </NuxtLink>
              <NuxtLink
                to="/orders"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2"
                :class="$route.path.startsWith('/orders') ? 'border-blue-500 text-gray-900' : 'border-transparent hover:border-gray-300 hover:text-gray-700'"
              >
                Orders
              </NuxtLink>
              <NuxtLink
                to="/reports"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2"
                :class="$route.path.startsWith('/reports') ? 'border-blue-500 text-gray-900' : 'border-transparent hover:border-gray-300 hover:text-gray-700'"
              >
                Reports
              </NuxtLink>
            </div>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <!-- Profile dropdown -->
            <div class="relative ml-3">
              <Menu as="div" class="relative">
                <div>
                  <MenuButton class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <span class="sr-only">Open user menu</span>
                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                      <span class="text-white text-sm font-medium">
                        {{ authStore.user?.name?.[0]?.toUpperCase() }}
                      </span>
                    </div>
                  </MenuButton>
                </div>
                <transition
                  enter-active-class="transition ease-out duration-200"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <MenuItem v-slot="{ active }">
                      <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm text-gray-700">{{ authStore.user?.name }}</p>
                        <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
                      </div>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        @click="logout"
                        :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                      >
                        Sign out
                      </button>
                    </MenuItem>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
          </div>
          <!-- Mobile menu button -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              type="button"
              class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
              <span class="sr-only">Open main menu</span>
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-show="mobileMenuOpen" class="sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
          <NuxtLink
            to="/dashboard"
            @click="mobileMenuOpen = false"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
            :class="$route.path === '/dashboard' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
          >
            Dashboard
          </NuxtLink>
          <NuxtLink
            to="/pos"
            @click="mobileMenuOpen = false"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
            :class="$route.path === '/pos' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
          >
            POS
          </NuxtLink>
          <NuxtLink
            to="/products"
            @click="mobileMenuOpen = false"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
            :class="$route.path.startsWith('/products') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
          >
            Products
          </NuxtLink>
          <NuxtLink
            to="/customers"
            @click="mobileMenuOpen = false"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
            :class="$route.path.startsWith('/customers') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
          >
            Customers
          </NuxtLink>
          <NuxtLink
            to="/orders"
            @click="mobileMenuOpen = false"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
            :class="$route.path.startsWith('/orders') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
          >
            Orders
          </NuxtLink>
          <NuxtLink
            to="/reports"
            @click="mobileMenuOpen = false"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
            :class="$route.path.startsWith('/reports') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'"
          >
            Reports
          </NuxtLink>
        </div>
        <div class="border-t border-gray-200 pb-3 pt-4">
          <div class="flex items-center px-4">
            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
              <span class="text-white text-sm font-medium">
                {{ authStore.user?.name?.[0]?.toUpperCase() }}
              </span>
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-gray-800">{{ authStore.user?.name }}</div>
              <div class="text-sm font-medium text-gray-500">{{ authStore.user?.email }}</div>
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <button
              @click="logout"
              class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 w-full text-left"
            >
              Sign out
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page content -->
    <main>
      <div class="mx-auto max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

const logout = async () => {
  await authStore.logout()
}
</script>