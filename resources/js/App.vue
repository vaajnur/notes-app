<script setup>
import { computed, onMounted, reactive, ref } from 'vue';

const notes = ref([]);
const loading = ref(true);
const saving = ref(false);
const pageError = ref('');
const formErrors = ref({});
const selectedNote = ref(null);
const editingId = ref(null);
const form = reactive({ title: '', content: '' });
const formTitle = computed(() => editingId.value ? 'Редактировать заметку' : 'Новая заметка');

async function api(path, options = {}) {
    const response = await fetch(`/api${path}`, { headers: { Accept: 'application/json', 'Content-Type': 'application/json' }, ...options });
    if (response.status === 204) return null;
    const payload = await response.json();
    if (!response.ok) throw { status: response.status, payload };
    return payload;
}

async function loadNotes() {
    loading.value = true; pageError.value = '';
    try { notes.value = (await api('/notes?per_page=100')).data; }
    catch (error) { pageError.value = 'Не удалось загрузить заметки. Попробуйте ещё раз.'; }
    finally { loading.value = false; }
}

function resetForm() { editingId.value = null; form.title = ''; form.content = ''; formErrors.value = {}; }
function editNote(note) { editingId.value = note.id; form.title = note.title; form.content = note.content ?? ''; formErrors.value = {}; window.scrollTo({ top: 0, behavior: 'smooth' }); }

async function saveNote() {
    saving.value = true; formErrors.value = {}; const id = editingId.value;
    try {
        await api(id ? `/notes/${id}` : '/notes', { method: id ? 'PUT' : 'POST', body: JSON.stringify(form) });
        resetForm(); await loadNotes();
    } catch (error) {
        if (error.status === 422) formErrors.value = error.payload.errors ?? {};
        else pageError.value = 'Не удалось сохранить заметку.';
    } finally { saving.value = false; }
}

async function viewNote(id) {
    pageError.value = '';
    try { selectedNote.value = (await api(`/notes/${id}`)).data; }
    catch (error) { pageError.value = 'Не удалось открыть заметку.'; }
}

async function deleteNote(note) {
    if (!window.confirm(`Удалить заметку «${note.title}»?`)) return;
    try {
        await api(`/notes/${note.id}`, { method: 'DELETE' });
        if (selectedNote.value?.id === note.id) selectedNote.value = null;
        if (editingId.value === note.id) resetForm();
        await loadNotes();
    } catch (error) { pageError.value = 'Не удалось удалить заметку.'; }
}

function formatDate(value) { return new Intl.DateTimeFormat('ru-RU', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value)); }
onMounted(loadNotes);
</script>

<template>
    <main class="page-shell">
        <header class="hero"><div><p class="eyebrow">Личное пространство</p><h1>Мои заметки</h1><p>Сохраняйте мысли, планы и важные детали.</p></div><span class="counter">{{ notes.length }} заметок</span></header>
        <section class="editor" aria-labelledby="editor-title">
            <div class="section-heading"><h2 id="editor-title">{{ formTitle }}</h2><button v-if="editingId" class="link-button" @click="resetForm">Отмена</button></div>
            <form @submit.prevent="saveNote">
                <label>Заголовок<input v-model.trim="form.title" maxlength="255" placeholder="О чём эта заметка?" required><small v-if="formErrors.title">{{ formErrors.title[0] }}</small></label>
                <label>Текст<textarea v-model="form.content" maxlength="10000" rows="5" placeholder="Запишите подробности..."></textarea><small v-if="formErrors.content">{{ formErrors.content[0] }}</small></label>
                <button class="primary" :disabled="saving">{{ saving ? 'Сохраняем…' : editingId ? 'Сохранить изменения' : 'Добавить заметку' }}</button>
            </form>
        </section>
        <p v-if="pageError" class="alert" role="alert">{{ pageError }} <button @click="loadNotes">Повторить</button></p>
        <section class="notes" aria-live="polite">
            <p v-if="loading" class="empty">Загружаем заметки…</p>
            <div v-else-if="!notes.length" class="empty"><strong>Здесь пока пусто</strong><span>Создайте первую заметку с помощью формы выше.</span></div>
            <article v-for="note in notes" v-else :key="note.id" class="note-card">
                <button class="card-main" @click="viewNote(note.id)"><h2>{{ note.title }}</h2><p>{{ note.content || 'Без текста' }}</p><time>{{ formatDate(note.updated_at) }}</time></button>
                <div class="actions"><button @click="editNote(note)">Изменить</button><button class="danger" @click="deleteNote(note)">Удалить</button></div>
            </article>
        </section>
        <div v-if="selectedNote" class="modal-backdrop" @click.self="selectedNote = null"><article class="modal" role="dialog" aria-modal="true" aria-labelledby="note-title"><button class="close" aria-label="Закрыть" @click="selectedNote = null">×</button><p class="eyebrow">Заметка</p><h2 id="note-title">{{ selectedNote.title }}</h2><p class="modal-content">{{ selectedNote.content || 'Без текста' }}</p><time>Обновлено {{ formatDate(selectedNote.updated_at) }}</time></article></div>
    </main>
</template>
