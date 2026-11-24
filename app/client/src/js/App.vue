<template>
    <div class="app" :class="{ 'app--tabbar': auth.state.user }">
        <AppHeader />
        <main id="main" class="area_content main">
            <RouterView />
        </main>
        <AppFooter />
        <AppTabBar v-if="auth.state.user" />
        <WishlistDialog v-if="auth.state.user" />
        <ToastStack />
    </div>
</template>

<script setup>
import { useAuth } from '@/stores/auth';
import AppHeader from '@/components/AppHeader.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppTabBar from '@/components/AppTabBar.vue';
import ToastStack from '@/components/ToastStack.vue';
import WishlistDialog from '@/components/WishlistDialog.vue';

const auth = useAuth();
</script>
