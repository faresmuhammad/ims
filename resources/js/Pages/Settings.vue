<template>

    <Head title="Settings"/>
    <app-layout>
        <div class="card h-full">
            <div class="flex justify-content-between">
                <h2>Settings</h2>
                <Button v-if="!editMode && can('edit settings')" icon="pi pi-pencil" rounded
                        class="w-2.5rem h-2.5rem p-0 mr-2"
                        @click="editMode = true"/>
            </div>
            <div v-for="(items, key) in settings">
                <h4>{{ key }}</h4>
                <ul class="list-none">
                    <setting-item v-for="setting in items" :key="setting.key" :setting="setting" :edit="editMode"
                                  v-model:value="changedSettings[setting.key]"/>
                </ul>
            </div>
            <div class="flex justify-content-end gap-4" v-if="editMode && can('edit settings')">
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
import {reactive, ref} from "vue";
import {usePermission} from "../composables/permissions";

const {can} = usePermission()
const props = defineProps({
    settings: Object
})
const editMode = ref(false)
const changedSettings = reactive({})

const updateSettings = () => {
    console.log(changedSettings)
    const requests = Object.keys(changedSettings).map((key) => {
        return axios.put(`/settings/${key}`, {
            value: changedSettings[key],
        })
            .then((response) => {
                console.log(`${key} setting is updated`)
                editMode.value = false
            }).catch(error => {
                console.log(error)
            });
    })
    Promise.all(requests)
        .then((responses) => {
            console.log('All requests are done')
        })
        .catch((errors) => {
            console.log('Error occurred while updating settings', errors)
        })
}
</script>
