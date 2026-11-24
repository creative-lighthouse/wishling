<template>
    <section class="section--Lists">
        <div class="section_content">
            <h1>Gemerkte Wunschlisten</h1>

            <PageState v-if="loading || loadError" :loading="loading" :error="loadError" />

            <template v-else>
                <div v-if="lists.length === 0" class="lists_empty">
                    <p>
                        Du hast dir noch keine Wunschliste gemerkt. Öffne eine geteilte Liste
                        und tippe auf „Merken“, dann findest du sie hier wieder.
                    </p>
                </div>

                <ul v-else class="lists_rows">
                    <li v-for="list in lists" :key="list.hash" class="list_row">
                        <RouterLink :to="{ name: 'wishlist-guest', params: { hash: list.hash } }" class="list_row_link">
                            <h2 class="list_row_title">{{ list.title }}</h2>
                            <p v-if="list.ownerName" class="list_row_owner">
                                von
                                <UserAvatar v-if="list.ownerAvatarUrl" :url="list.ownerAvatarUrl" :name="list.ownerName" size="inline" />
                                {{ list.ownerName }}
                            </p>
                            <p v-if="list.description" class="list_row_description">{{ list.description }}</p>
                            <p class="list_row_meta">
                                {{ list.wishCount === 1 ? '1 Wunsch' : `${list.wishCount} Wünsche` }}
                                <template v-if="list.wishCount"> · {{ list.freeCount }} noch frei</template>
                            </p>
                        </RouterLink>
                        <div class="list_row_actions">
                            <ShareActions :url="list.shareUrl" :title="list.title" icon-only />
                            <button
                                type="button"
                                class="button button--icon-bookmarked button--icon-only"
                                :aria-label="`„${list.title}“ nicht mehr merken`"
                                title="Nicht mehr merken"
                                @click="unsave(list)"
                            />
                        </div>
                    </li>
                </ul>
            </template>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { savedApi } from '@/api';
import { useAuth } from '@/stores/auth';
import { useToasts } from '@/composables/useToasts';
import PageState from '@/components/PageState.vue';
import ShareActions from '@/components/ShareActions.vue';
import UserAvatar from '@/components/UserAvatar.vue';

const auth = useAuth();
const toast = useToasts();

const lists = ref([]);
const loading = ref(true);
const loadError = ref('');

async function load() {
    const data = await savedApi.list();
    lists.value = data.wishlists;
}

onMounted(async () => {
    try {
        await load();
    } catch (e) {
        loadError.value = e.message;
    } finally {
        loading.value = false;
    }
});

async function unsave(list) {
    try {
        const result = await savedApi.unsave(list.hash);
        lists.value = lists.value.filter((item) => item.hash !== list.hash);
        auth.setSavedCount(result.savedCount);
        toast.success(`„${list.title}“ ist nicht mehr gemerkt.`, {
            action: { label: 'Rückgängig', handler: () => resave(list) },
        });
    } catch (e) {
        toast.error(e.message);
    }
}

async function resave(list) {
    try {
        const result = await savedApi.save(list.hash);
        auth.setSavedCount(result.savedCount);
        // Neu laden, damit Reihenfolge und Zahlen stimmen
        await load();
    } catch (e) {
        toast.error(e.message);
    }
}
</script>
