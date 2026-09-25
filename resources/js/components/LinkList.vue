<script setup>
import {
    Copy,
    ExternalLink,
    ArrowUpRight,
    Pause,
    Play,
    Archive,
    RotateCcw,
} from "@lucide/vue";
import { compactUrl, displayDate } from "../lib/api";
defineProps({ links: Array, busyId: Number });
const emit = defineEmits(["copy", "edit", "status"]);
</script>
<template>
    <div class="link-list">
        <article
            v-for="link in links"
            :key="link.id"
            class="link-row"
            :aria-label="link.title"
        >
            <div class="link-symbol"><ArrowUpRight :size="22" /></div>
            <div class="link-info">
                <div class="link-title-line">
                    <button class="link-title" @click="emit('edit', link)">
                        {{ link.title }}</button
                    ><span class="status-badge" :class="link.status">{{
                        link.status
                    }}</span>
                </div>
                <div class="link-address">
                    <a
                        :href="link.short_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        >{{ compactUrl(link.short_url)
                        }}<ExternalLink :size="12" /></a
                    ><button
                        class="copy-button"
                        :aria-label="`Copy ${link.alias}`"
                        @click="emit('copy', link)"
                    >
                        <Copy :size="14" />
                    </button>
                </div>
                <p class="destination" :title="link.url">{{ link.url }}</p>
            </div>
            <div class="link-metrics">
                <strong>{{ link.clicks.toLocaleString() }}</strong
                ><span>clicks</span>
            </div>
            <time class="link-date" :datetime="link.created_at">{{
                displayDate(link.created_at)
            }}</time>
            <div class="link-actions">
                <button
                    class="icon-button"
                    :aria-label="`Edit ${link.title}`"
                    @click="emit('edit', link)"
                >
                    <ArrowUpRight :size="18" /></button
                ><button
                    v-if="link.status !== 'archived'"
                    class="icon-button"
                    :disabled="busyId === link.id"
                    :aria-label="`${link.status === 'paused' ? 'Resume' : 'Pause'} ${link.title}`"
                    @click="
                        emit(
                            'status',
                            link,
                            link.status === 'paused' ? 'active' : 'paused',
                        )
                    "
                >
                    <Play v-if="link.status === 'paused'" :size="16" /><Pause
                        v-else
                        :size="16"
                    /></button
                ><button
                    class="icon-button"
                    :disabled="busyId === link.id"
                    :aria-label="`${link.status === 'archived' ? 'Restore' : 'Archive'} ${link.title}`"
                    @click="
                        emit(
                            'status',
                            link,
                            link.status === 'archived' ? 'active' : 'archived',
                        )
                    "
                >
                    <RotateCcw
                        v-if="link.status === 'archived'"
                        :size="17"
                    /><Archive v-else :size="17" />
                </button>
            </div>
        </article>
    </div>
</template>
