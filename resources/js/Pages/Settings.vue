<template>

    <Head title="Settings"/>
    <app-layout>
        <div class="card h-full">
            <div class="flex justify-content-between">
                <h2>Settings</h2>
                <Button v-if="!editMode" icon="pi pi-pencil" rounded class="w-2.5rem h-2.5rem p-0 mr-2"
                        @click="editMode = true"/>
            </div>
            <div v-for="(items, key) in settings">
                <h4>{{ key }}</h4>
                <ul class="list-none">
                    <setting-item v-for="setting in items" :key="setting.key" :setting="setting" :edit="editMode" v-model:value="changedSettings"/>
                </ul>
            </div>
            <div class="flex justify-content-end gap-4" v-if="editMode">
                <Button label="Apply" security="primary" @click="updateSettings"/>
                <Button label="Cancel" outlined @click="editMode = false"/>
            </div>
        </div>
    </app-layout>
</template>


<script setup>
import {Head} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingItem from "@/Components/SettingItem.vue";
import {ref} from "vue";

const props = defineProps({
    settings: Object
})
const editMode = ref(false)
const changedSettings = ref()

const updateSettings = () => {

}
</script>
