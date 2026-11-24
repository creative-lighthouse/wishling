<template>
    <nav class="tab_bar" aria-label="Hauptnavigation (mobil)">
        <RouterLink :to="{ name: 'my-lists' }" class="tab_bar_link" :class="{ 'is-active': isMyLists }">
            <span class="tab_bar_icon tab_bar_icon--lists" aria-hidden="true"></span>
            <span class="tab_bar_label">Eigene Listen</span>
        </RouterLink>
        <RouterLink :to="{ name: 'saved-lists' }" class="tab_bar_link">
            <span class="tab_bar_icon tab_bar_icon--saved" aria-hidden="true"></span>
            <span class="tab_bar_label">Gemerkte Listen</span>
        </RouterLink>
        <RouterLink :to="{ name: 'profile' }" class="tab_bar_link">
            <UserAvatar :url="auth.state.user.avatarUrl || ''" :name="displayName" size="small" class="tab_bar_avatar" />
            <span class="tab_bar_label">Profil</span>
        </RouterLink>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuth } from '@/stores/auth';
import UserAvatar from '@/components/UserAvatar.vue';

const auth = useAuth();
const route = useRoute();

const displayName = computed(() => auth.state.user?.displayName || 'Profil');

// Auch beim Bearbeiten einer eigenen Liste ist der Tab aktiv
const isMyLists = computed(() => route.name === 'wishlist-edit');
</script>
