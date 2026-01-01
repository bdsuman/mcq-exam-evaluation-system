<template>
    <div id="vue-app-container">
        <router-view v-slot="{ Component }">
            <Suspense>
                <template #default>
                    <component :is="Component" />
                </template>
                <template #fallback>
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500 mx-auto"></div>
                            <p class="mt-4 text-gray-600">Loading...</p>
                        </div>
                    </div>
                </template>
            </Suspense>
        </router-view>
    </div>
</template>