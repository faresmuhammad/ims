<template>

    <Head title="Categories" />
    <app-layout>
        <div class="grid">
            <div class="col-12">
                <div class="card">
                    <h2>Categories</h2>

                    <Toolbar class="mb-4">
                        <template v-slot:end>
                            <!--//TODO: implement open new dialog and item selection to delete-->
                            <div class="my-2">
                                <Button label="New" icon="pi pi-plus" class="p-button-success mr-2" @click="openNew" />
                                <Button label="Delete" icon="pi pi-trash" class="p-button-danger"
                                    @click="confirmDeleteSelected"
                                    :disabled="!selectedProducts || !selectedProducts.length" />
                            </div>
                        </template>

                    </Toolbar>

                    <DataTable :value="categories">
                        <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header"/>
                    </DataTable>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script setup>
import AppLayout from '../Components/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const props = defineProps({
    categories: Object,
});

const columns = [
    { field: 'name', header: 'Name' },
    { field: 'slug', header: 'Slug'}
]

const openNew = () => {
    //TODO: implement open new dialog
}

const confirmDeleteSelected = () => {
    //TODO: implement open new dialog and item selection to delete

}
</script>