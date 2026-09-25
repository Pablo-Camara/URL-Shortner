<script setup>
import { onMounted, onUnmounted, reactive, ref } from "vue";
import { X, ArrowUpRight, History } from "@lucide/vue";
import { api, displayDate, localDateTime } from "../lib/api";
const props = defineProps({ link: { type: Object, default: null } });
const emit = defineEmits(["close", "saved"]);
const modal = ref(null),
    busy = ref(false),
    error = ref("");
const form = reactive({
    title: props.link?.title ?? "",
    url: props.link?.url ?? "",
    alias: "",
    expires_at: localDateTime(props.link?.expires_at),
});
let priorFocus;
onMounted(() => {
    priorFocus = document.activeElement;
    modal.value.showModal();
});
onUnmounted(() => priorFocus?.focus());
async function save() {
    busy.value = true;
    error.value = "";
    const body = {
        title: form.title,
        url: form.url,
        expires_at: form.expires_at
            ? new Date(form.expires_at).toISOString()
            : null,
    };
    if (props.link) body.version = props.link.version;
    else body.alias = form.alias || null;
    try {
        const result = await api(
            props.link ? `/links/${props.link.id}` : "/links",
            { method: props.link ? "PATCH" : "POST", body },
        );
        emit("saved", result.data);
    } catch (e) {
        error.value = e.message;
    } finally {
        busy.value = false;
    }
}
</script>
<template>
    <dialog
        ref="modal"
        class="editor"
        aria-labelledby="editor-title"
        @cancel.prevent="!busy && emit('close')"
    >
        <header class="editor-header">
            <div>
                <p class="eyebrow">A LITTLE LINK. A BIG NEXT STEP.</p>
                <h2 id="editor-title">
                    {{ link ? "Refine your link." : "Create a new link." }}
                </h2>
            </div>
            <button
                class="icon-button"
                aria-label="Close editor"
                :disabled="busy"
                @click="emit('close')"
            >
                <X />
            </button>
        </header>
        <form @submit.prevent="save">
            <label
                >Title<input
                    v-model="form.title"
                    required
                    maxlength="120"
                    placeholder="Give this link a name"
                    autofocus
            /></label>
            <label
                >Destination URL<input
                    v-model="form.url"
                    type="url"
                    required
                    maxlength="2048"
                    placeholder="https://example.com/something-great"
            /></label>
            <label v-if="!link"
                >Custom short link <span class="optional">optional</span>
                <div class="alias-input">
                    <span>/</span
                    ><input
                        v-model="form.alias"
                        pattern="[a-z0-9]+(-[a-z0-9]+)*"
                        minlength="3"
                        maxlength="40"
                        placeholder="your-next-idea"
                        aria-describedby="alias-help"
                    /></div
            ></label>
            <p v-if="!link" id="alias-help" class="field-hint">
                3–40 lowercase letters, numbers or hyphens. Leave blank to
                generate one.
            </p>
            <p v-else class="fixed-alias">
                Short link <strong>/{{ link.alias }}</strong
                ><span
                    >The address stays the same when you edit its
                    destination.</span
                >
            </p>
            <label
                >Expiration
                <span class="optional">optional · your local time</span
                ><input v-model="form.expires_at" type="datetime-local"
            /></label>
            <p class="field-hint">
                Leave blank to keep the link active indefinitely.
            </p>
            <p v-if="error" role="alert" class="error">{{ error }}</p>
            <footer class="editor-actions">
                <button
                    type="button"
                    class="secondary"
                    :disabled="busy"
                    @click="emit('close')"
                >
                    Cancel</button
                ><button class="primary" :disabled="busy">
                    {{ busy ? "Saving…" : link ? "Save changes" : "Create link"
                    }}<ArrowUpRight :size="17" />
                </button>
            </footer>
        </form>
        <section v-if="link?.history?.length" class="history">
            <h3><History :size="16" /> Destination history</h3>
            <ol>
                <li v-for="(item, index) in link.history" :key="index">
                    <span>{{
                        item.current
                            ? "Current destination"
                            : displayDate(item.created_at)
                    }}</span>
                    <p>{{ item.url }}</p>
                </li>
            </ol>
        </section>
    </dialog>
</template>
