<template>

    <Head title="Suppliers" />
    <app-layout>
        <div class="grid">
            <div class="col-12">
                <div class="card">
                    <h2>Suppliers</h2>

                    <Toolbar class="mb-4">
                        <template v-slot:end>
                            <div class="my-2">
                                <Button label="New" icon="pi pi-plus" class="p-button-success mr-2"
                                    @click="newSupplierDialog = true" />
                                <Button label="Delete" icon="pi pi-trash" class="p-button-danger"
                                    v-if="selectedSuppliers.length > 0" @click="deleteConfirmation = true" />
                            </div>
                        </template>

                    </Toolbar>

                    <DataTable v-model:selection="selectedSuppliers" v-model:editingRows="editingRows"
                        :value="suppliers" editMode="row" @row-edit-save="updateSupplier" dataKey="id">
                        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                        <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header">
                            <template #body="{ data, field }">
                                {{ data[field] }}
                            </template>
                            <template #editor="{ data, field }">
                                <template v-if="field !== 'code'">
                                    <InputText v-model="data[field]" autofocus />
                                </template>
                                <template v-else>
                                    {{ data[field] }}
                                </template>
                            </template>
                        </Column>
                        <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center">
                        </Column>
                    </DataTable>

                    <Dialog v-model:visible="newSupplierDialog" header="Supplier Information" :style="{ width: '450px' }"
                        class="p-fluid">
                        <div class="field">
                            <label for="name">Name</label>
                            <InputText id="name" v-model.trim="supplier.name" required="true" autofocus />
                        </div>

                        <div class="field">
                            <label for="phonenumber">Phone Number</label>
                            <InputText id="phonenumber" v-model="supplier.phone"  />
                        </div>


                        <template #footer>
                            <Button label="Cancel" icon="pi pi-times" outlined @click="hideSupplierDialog" />
                            <Button label="Save" icon="pi pi-check" @click="saveSupplier" />
                        </template>
                    </Dialog>


                    <Dialog header="Confirmation" v-model:visible="deleteConfirmation" :style="{ width: '450px' }"
                        :modal="true">
                        <div class="flex align-items-center justify-content-center">
                            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 3rem" />
                            <span>Are you sure you will delete {{ selectedSuppliers.length }} suppliers?</span>
                        </div>
                        <template #footer>
                            <Button label="No" icon="pi pi-times" @click="deleteConfirmation = false" outlined />
                            <Button label="Yes" icon="pi pi-check" @click="deleteSuppliers" autofocus />
                        </template>
                    </Dialog>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive } from 'vue';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const props = defineProps({
    suppliers: Object,
});

const page = usePage();

const editingRows = ref([])
const selectedSuppliers = ref([])

const newSupplierDialog = ref(false)
const deleteConfirmation = ref(false)

const supplier = reactive({
    name: '',
    phone: null,
})

const columns = [
    { field: 'code', header: 'Code' },
    { field: 'name', header: 'Name' },
    { field: 'phone', header: 'Phone Number' },
]


const saveSupplier = () => {
    console.log('save supplier')
    router.post('/suppliers', supplier, {
        onSuccess: (pageObj) => {
            toast.add({ severity: pageObj.props.flash.severity, summary: pageObj.props.flash.message, life: 4000 });
            hideSupplierDialog()
        }
    })
}

const hideSupplierDialog = () => {
    newSupplierDialog.value = false
    supplier.name = ''
    supplier.phone = null
}

const updateSupplier = (event) => {
    let { newData, index } = event
    console.log(newData, index)
    router.put('/suppliers/' + newData.id, {
        name: newData.name,
        phone: newData.phone,
    }, {
        onSuccess: (pageObj) => {
            toast.add({ severity: pageObj.props.flash.severity, summary: pageObj.props.flash.message, life: 4000 });
        }
    })
}

const deleteSuppliers = () => {
    if (selectedSuppliers.value.length > 0) {
        const ids = selectedSuppliers.value.map((obj) => obj.id).join(',')
        console.log(selectedSuppliers.value, ids)
        router.delete('/suppliers/' + ids)
        deleteConfirmation.value = false
        selectedSuppliers.value = []
        toast.add({ severity: page.props.flash.severity, summary: page.props.flash.message, life: 4000 });
    }
}
</script>