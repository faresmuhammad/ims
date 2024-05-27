<template>

    <Head title="Products"/>
    <app-layout>
        <div class="grid">
            <div class="col-12">
                <div class="card">
                    <h2>Products</h2>

                    <Toolbar class="mb-4">
                        <template v-slot:end>
                            <FileUpload v-if="can('import products')" name="products[]" auto :customUpload="true"
                                        mode="basic"
                                        chooseLabel="Import Products" class="mr-2"
                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                        :maxFileSize="1000000" @uploader="uploadFile"/>
                            <Button v-if="can('add product')" label="New" icon="pi pi-plus"
                                    class="p-button-success mr-2"
                                    @click="newProductDialog = true"/>
                        </template>

                    </Toolbar>

                    <!-- Filter by category, supplier, expired date -->
                    <div class="flex flex-row gap-3 justify-content-start">
                        <Dropdown v-model="filters['category_id'].value" :options="categories" filter showClear
                                  filterIcon="pi pi-filter" autoFilterFocus selectOnFocus resetFilterOnHide
                                  placeholder="Select a Category" optionLabel="name" optionValue="id"/>


                    </div>

                    <DataTable :value="products" dataKey="id" :filters="filters"
                               :globalFilterFields="globalFilterFields">
                        <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header">
                            <template #body="{ data, field }">
                                <template v-if="field === 'name'">
                                    <Link v-if="can('see product info')" :href="`/products/${data['slug']}`">
                                        {{ data[field] }}
                                    </Link>
                                    <div v-else>{{ data[field]}}</div>
                                </template>
                                <template v-else>
                                    {{ data[field] }}
                                </template>
                            </template>
                        </Column>
                        <template #empty>
                            <h5 class="flex align-items-center justify-content-center">No Products Found</h5>
                        </template>
                    </DataTable>

                    <!-- TODO: add parts per unit field -->
                    <Dialog v-model:visible="newProductDialog" header="Product Information" :style="{ width: '450px' }"
                            class="p-fluid">
                        <div class="field">
                            <label for="code">Code</label>
                            <InputText id="code" v-model="product.code" autofocus/>
                        </div>

                        <div class="field">
                            <label for="name">Name</label>
                            <InputText id="name" v-model.trim="product.name" required="true"/>
                        </div>

                        <div class="field">
                            <label for="description">Description</label>
                            <Textarea id="description" v-model="product.description" rows="3" cols="12" autoResize/>
                        </div>
                        <div class="field">
                            <label for="category">Category</label>
                            <Dropdown v-model="product.category_id" :options="categories" editable
                                      placeholder="Select a Category" optionLabel="name" optionValue="id"/>
                        </div>
                        <div class="formgrid grid">
                            <div class="field col">
                                <label for="price">Price</label>
                                <InputNumber id="price" v-model="product.price" mode="currency" currency="EGP"
                                             locale="en-US" :class="{ 'p-invalid': submitted && !product.price }"
                                             :required="true"/>
                            </div>
                            <div class="field col">
                                <label for="quantity">Stock Offset</label>
                                <InputNumber id="quantity" v-model="product.stock_offset" integeronly/>
                            </div>
                        </div>
                        <template #footer>
                            <Button label="Cancel" icon="pi pi-times" outlined @click="hideProductDialog"/>
                            <Button label="Save" icon="pi pi-check" @click="saveProduct"/>
                        </template>
                    </Dialog>


                </div>
            </div>
        </div>
    </app-layout>
</template>


<script setup>
import {Head, router, usePage, Link} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {ref, reactive, watch} from 'vue';
import {useToast} from 'primevue/usetoast';
import {FilterMatchMode} from 'primevue/api';
import {usePermission} from "../composables/permissions";

const {can} = usePermission()

const toast = useToast();
const props = defineProps({
    products: Object,
    categories: Object
});

const page = usePage();

const newProductDialog = ref(false)


const globalFilterFields = ref(['name', 'category_id'])
const filters = ref({
    'name': {value: null, matchMode: FilterMatchMode.CONTAINS},
    'category_id': {value: null, matchMode: FilterMatchMode.EQUALS}
})


const product = reactive({
    code: '',
    name: '',
    description: null,
    stock_offset: 0,
    price: 0,
    category_id: null
})

const columns = [
    {field: 'code', header: 'Code'},
    {field: 'name', header: 'Name'},
    // { field: 'available_quantity', header: 'Available Quantity' },
]


const saveProduct = () => {
    router.post('/products', product, {
        onSuccess: (pageObj) => {
            toast.add({severity: pageObj.props.flash.severity, summary: pageObj.props.flash.message, life: 4000});
            hideProductDialog()
        }
    })
}

const hideProductDialog = () => {
    newProductDialog.value = false
    product.code = ''
    product.name = ''
    product.description = null
    product.stock_offset = 0
    product.price = 0
    product.category_id = null
}

const uploadFile = (event) => {
    console.log(event)
    const file = event.files[0];
    router.post('/products/upload', {
        products: file
    })
}

//TODO: Apply Supplier and expired date filters
</script>
