<template>
    <header class="area_header">
        <div class="section_content">
            <RouterLink to="/" class="logo">
                <img src="/_resources/app/client/icons/wishling_logo.svg" alt="Wishling – zur Startseite">
            </RouterLink>
            <nav class="header_nav" aria-label="Hauptnavigation">
                <template v-if="auth.state.user">
                    <RouterLink :to="{ name: 'my-lists' }" class="header_link">Meine Listen</RouterLink>
                    <RouterLink v-if="showSaved" :to="{ name: 'saved-lists' }" class="header_link">
                        Gemerkte Listen
                    </RouterLink>
                    <RouterLink
                        :to="{ name: 'profile' }"
                        class="header_link header_link--profile"
                        :aria-label="`Profil von ${displayName}`"
                    >
                        <UserAvatar :url="auth.state.user.avatarUrl || ''" :name="displayName" size="small" />
                        <span class="header_profile_name" aria-hidden="true">{{ displayName }}</span>
                    </RouterLink>
                </template>
                <RouterLink v-else-if="auth.state.loaded" :to="{ name: 'login' }" class="button button--primary">
                    Anmelden
                </RouterLink>
            </nav>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuth } from '@/stores/auth';
import UserAvatar from '@/components/UserAvatar.vue';

const auth = useAuth();
const route = useRoute();

const displayName = computed(() => auth.state.user?.displayName || 'Profil');

// Erst sichtbar, sobald mindestens eine Liste gemerkt ist.
// Auf der Seite selbst bleibt der Link stehen, auch wenn gerade die letzte entfernt wurde.
const showSaved = computed(() => (auth.state.user?.savedCount ?? 0) > 0 || route.name === 'saved-lists');
</script>
