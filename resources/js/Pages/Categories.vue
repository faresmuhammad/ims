<template>

    <Head title="Categories" />
    <app-layout>
        <div class="grid">
            <div class="col-12">
                <div class="card">
                    <h2>Categories</h2>

                    <Toolbar class="mb-4">
                        <template v-slot:end>
                            <div class="my-2">
                                <Button label="New" icon="pi pi-plus" class="p-button-success mr-2"
                                    @click="newCategoryDialog = true" />
                                <Button label="Delete" icon="pi pi-trash" class="p-button-danger"
                                    v-if="selectedCategories.length > 0" @click="deleteConfirmation = true" />
                            </div>
                        </template>

                    </Toolbar>

                    <DataTable v-model:selection="selectedCategories" v-model:editingRows="editingRows"
                        v-model:expandedRows="expandingRows" :value="categories" editMode="row"
                        @row-edit-save="updateCategory" dataKey="id">
                        <Column expander style="width: 1rem" />
                        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                        <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header">
                            <template #body="{ data, field }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-if="field !== 'description'" v-model="data[field]" autofocus />
                                <Textarea v-else v-model="data[field]" rows="1" cols="50" autoResize autofocus />
                            </template>
                        </Column>
                        <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                        </Column>
                        <!-- FIXME: selection and editing doesn't work in expansion slot -->
                        <!-- FIXME: hide table header row -->
                        <template #expansion="slotProps">
                            <DataTable :value="slotProps.data.subcategories" v-model:selection="selectedSubcategories"
                                v-model:editingRows="editingRowss" headerClass="noHeader" editMode="row"
                                @row-edit-save="updateCategory" dataKey="id">
                                <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                                <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header">
                                    <template #body="{ data, field }">
                                        {{ data[field] }}
                                    </template>
                                    <template #editor="{ data, field }">
                                        <InputText v-if="field !== 'description'" v-model="data[field]" autofocus />
                                        <Textarea v-else v-model="data[field]" rows="1" cols="50" autoResize
                                            autofocus />
                                    </template>
                                </Column>
                                <Column :rowEditor="true" style="width: 10%; min-width: 8rem"
                                    bodyStyle="text-align:center">
                                </Column>
                            </DataTable>
                        </template>
                    </DataTable>

                    <Dialog v-model:visible="newCategoryDialog" header="Category Details" :style="{ width: '450px' }"
                        class="p-fluid">
                        <div class="field">
                            <label for="name">Name</label>
                            <InputText id="name" v-model.trim="category.name" required="true" autofocus />
                            <!-- <small class="p-invalid" v-if="submitted && !product.name">Name is required.</small> -->
                        </div>

                        <div class="field">
                            <label for="description">Description</label>
                            <Textarea id="description" v-model="category.description" required="true" rows="3" cols="20"
                                autoResize />
                        </div>

                        <div class="field">
                            <label for="parent-category">Parent Category</label>
                            <Dropdown v-model="category.parent_id" :options="categories" editable
                                placeholder="Select a Parent Category" optionLabel="name" optionValue="id" />
                        </div>

                        <template #footer>
                            <Button label="Cancel" icon="pi pi-times" outlined @click="hideCategoryDialog" />
                            <Button label="Save" icon="pi pi-check" @click="saveCategory" />
                        </template>
                    </Dialog>


                    <Dialog header="Confirmation" v-model:visible="deleteConfirmation" :style="{ width: '450px' }"
                        :modal="true">
                        <div class="flex align-items-center justify-content-center">
                            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 3rem" />
                            <span>Are you sure you will delete {{ selectedCategories.length }} categories?</span>
                        </div>
                        <template #footer>
                            <Button label="No" icon="pi pi-times" @click="deleteConfirmation = false" outlined />
                            <Button label="Yes" icon="pi pi-check" @click="deleteCategories" autofocus />
                        </template>
                    </Dialog>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { reactive, ref } from 'vue';
import { useToast } from 'primevue/usetoast';


const toast = useToast();

const props = defineProps({
    categories: Object,
});

const page = usePage();

const editingRows = ref([])
const editingRowss = ref([])
const selectedCategories = ref([])
const selectedSubcategories = ref([])
const expandingRows = ref([])

const newCategoryDialog = ref(false)
const deleteConfirmation = ref(false)

const category = reactive({
    name: '',
    description: null,
    parent_id: null
})

const columns = [
    { field: 'name', header: 'Name' },
    { field: 'description', header: 'Description' },
]




const saveCategory = () => {
    router.post('/categories', category, {
        onSuccess: (pageObj) => {
            toast.add({ severity: pageObj.props.flash.severity, summary: pageObj.props.flash.message, life: 4000 });
            hideCategoryDialog()
        }
    })
}

const hideCategoryDialog = () => {
    newCategoryDialog.value = false
    category.name = ''
    category.description = null
    category.parent_id = null
}

const updateCategory = (event) => {
    let { newData, index } = event

    router.put('/categories/' + newData.id, {
        name: newData.name,
        description: newData.description,
    }, {
        onSuccess: (pageObj) => {
            toast.add({ severity: pageObj.props.flash.severity, summary: pageObj.props.flash.message, life: 4000 });
        }
    })
}

const deleteCategories = () => {
    if (selectedCategories.value.length > 0) {
        const ids = selectedCategories.value.map((obj) => obj.id).join(',')
        console.log(selectedCategories.value, ids)
        router.delete('/categories/' + ids)
        deleteConfirmation.value = false
        selectedCategories.value = []
        toast.add({ severity: page.props.flash.severity, summary: page.props.flash.message, life: 4000 });
    }
}
</script>

<style>
.noHeader {
    display: 'none';
}
</style>