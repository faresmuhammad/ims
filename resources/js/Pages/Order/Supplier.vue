<template>

    <Head :title="order ? 'Order #' + order.reference_code : 'No Order'" />
    <div class="card m-3">
        <!-- Header -->
        <Toast />
        <div class="flex justify-content-between">

            <h2>Supplier</h2>
            <Dropdown v-model="selectedSupplier" :options="suppliers" placeholder="Select a Supplier"
                @update:modelValue="updateSupplierOfOrder" optionLabel="name" optionValue="id" />
            <h5>{{order ? 'Order #' + order.reference_code : 'No Order'}}</h5>

            <!-- Show created since [time] -->

            <!-- Show updated since [time] -->
        </div>
            <Tag :value="order.completed ? 'Completed' : 'Incomplete'" :severity="order.completed ? 'success' : 'warning'" :icon="order.completed ? 'pi pi-check' : 'pi pi-times'"/>
        <!-- Table of order items -->
        <DataTable v-model:selection="selectedItem" selectionMode="single" :value="items" stripedRows showGridlines
            editMode="cell" @cell-edit-complete="updateItem" @cell-edit-init="beginEdit"
            @keyup.ctrl.delete.exact="deleteItem">
            <template #header>
                <div class="flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="text-xl text-900 font-bold">{{ header }}</span>
                </div>
            </template>

            <Column field="product" key="product.code" header="Code" class="col">
                <template #body="{ field, data }">
                    {{ data[field].code }}
                </template>
                <template #editor="{ field, data }">
                    <InputText v-model="data[field].code" />
                </template>
            </Column>
            <Column field="product.name" header="Name" class="col-3"></Column>
            <Column field="quantity" header="Quantity" class="col-1">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputmode="integer" />
                </template>
            </Column>
            <Column field="parts" header="Parts" class="col-1">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputId="integer" />
                </template>
            </Column>
            <Column field="discount" header="Discount" class="col-1">
                <template #body="{ field, data }">
                    %{{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputId="percent" prefix="%" :min="0" :max="100" />
                </template>
            </Column>
            <Column field="product.price" header="Selling Price" class="col-1"></Column>
            <Column field="expire_date" header="Exp. Date" class="col-2">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    //TODO: handle passing the date value to db
                    <Calendar v-model="data[field]" view="month" dateFormat="mm/yy" />
                </template>
            </Column>
            <Column field="total_price" header="Total Price" class="col-1"></Column>


            <template #footer>
                <div @keyup.ctrl.enter="submitItem" @keyup.enter.exact="getProduct('new')" class="card p-3"
                    style="border-color: var(--primary-color)">

                    <div class="formgrid grid">
                        <div class="field col-2">
                            <label for="code">Code</label>
                            <InputText v-model="newItem.code" class="w-full" id="code" />
                        </div>
                        <div class="field col-3">
                            <label for="name">Name</label>
                            <InputText v-model="newItem.name" class="w-full" id="name" disabled />
                        </div>
                        <div class="field col-1 m-0">
                            <label for="quantity">Quantity</label>
                            <InputNumber v-model="newItem.quantity" class="w-full" id="quantity" inputId="integer" />
                        </div>
                        <div class="field col-1">
                            <label for="parts">Parts</label>
                            <InputNumber v-model="newItem.parts" class="w-full" id="parts" inputId="integer" />
                        </div>
                        <div class="field col-1">
                            <label for="discount">Discount</label>
                            <InputNumber v-model="newItem.discount" class="w-full" id="discount" inputId="percent"
                                prefix="%" :min="0" :max="100" />
                        </div>
                        <div class="field col-1">
                            <label for="selling-price">Selling Price</label>
                            <InputText v-model="newItem.unit_price" class="w-full" id="selling-price" />
                        </div>
                        <div class="field col-2">
                            <label for="exp-date">Exp. Date</label>
                            <Calendar v-model="newItem.expDate" view="month" dateFormat="mm/yy" id="exp-date" />
                        </div>
                        <div class="field col-1">
                            <label for="total-price">Total Price</label>
                            <InputText v-model="newItem.totalPrice" class="w-full" id="total-price" />
                        </div>
                    </div>
                </div>
            </template>
        </DataTable>
        <!-- Total Price -->
        <!-- Save and cancel buttons -->
    </div>
</template>


<script setup>
import { Head, router } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import { arrow } from '@popperjs/core';
import InputNumber from 'primevue/inputnumber';
const props = defineProps({
    order: Object,
    suppliers: Object,
})
const toast = useToast();

const selectedSupplier = ref(null)
//TODO: create stocks of order items 

const updateSupplierOfOrder = () => {
    router.put('/orders/' + props.order.id, {
        supplier_id: selectedSupplier.value
    }, {
        onSuccess: (page) => {
            console.log(page)
        }
    })
}
//TODO: handle calendar input data
//TODO: handle errors feedback for:
//  - no product found
//  - available quantity in db

const selectedItem = ref()
const newItem = reactive({
    product_id: null,
    code: '',
    name: '',
    quantity: 1,
    parts: 0,
    discount: 0,
    unit_price: 0,
    totalPrice: 0,
    expDate: ''
})

let current = reactive({
    id: null,
    product_id: null,
    code: '',
    name: '',
    quantity: 1,
    parts: 0,
    discount: 0,
    unit_price: 0,
    totalPrice: 0,
    expDate: ''
})

const items = ref(null)
onMounted(() => {
    getItems()
})

const getItems = () => {
    axios.get('/order/' + props.order.reference_code + '/items')
        .then((response) => {
            items.value = response.data
        })
}
const getProduct = async (status) => {
    if (status === 'new') {
        axios.get('/product/' + newItem.code)
            .then((response) => {
                //TODO: check if the product exist in the order --> focus on the item to edit
                const product = response.data
                newItem.product_id = product.id
                newItem.name = product.name
                newItem.unit_price = product.price
            }).catch((e) => {
                console.log(e)
            })
    } else {
        const response = await axios.get('/product/' + current.code)
        const product = response.data
        current.product_id = product.id
        current.name = product.name
        current.unit_price = product.price
        console.log(current)

    }
}

const submitItem = () => {
    axios.post('/order/' + props.order.reference_code + '/items', {
        order_id: props.order.id,
        product_id: newItem.product_id,
        quantity: newItem.quantity,
        parts: newItem.parts,
        discount: newItem.discount,
        unit_price: newItem.unit_price,
        total_amount: newItem.totalPrice,
        expire_date: newItem.expDate
    })
        .then((response) => {
            items.value = response.data.items
            resetForm()
        })
        .catch((e) => {
            console.log(e)
        })
}

const resetForm = () => {
    newItem.product_id = null
    newItem.code = ''
    newItem.name = ''
    newItem.quantity = 0
    newItem.parts = 0
    newItem.discount = 0
    newItem.unit_price = 0
    newItem.totalPrice = 0
    newItem.expDate = ''
}

const beginEdit = (event) => {
    const item = event.data
    current = {
        id: item.id,
        product_id: item.product_id,
        code: item.product.code,
        name: item.product.name,
        quantity: item.quantity,
        parts: item.parts,
        discount: item.discount,
        unit_price: item.unit_price,
        totalPrice: item.total_amount,
        expDate: item.expire_date
    }
}

const updateItem = async (event) => {

    if (event.field === 'product') {
        current.code = event.newData.product.code
        const response = await axios.get('/product/' + current.code)
        const product = response.data
        current.product_id = product.id
        current.name = product.name
        current.unit_price = product.price

    }
    if (event.field === 'quantity') current.quantity = event.newData.quantity;
    if (event.field === 'parts') current.parts = event.newData.parts;
    if (event.field === 'discount') current.discount = event.newData.discount;
    if (event.field === 'expire_date') current.expDate = event.newData.expDate;


    router.put('/order/item/' + current.id, {
        product_id: current.product_id,
        quantity: current.quantity,
        parts: current.parts,
        unit_price: current.unit_price,
        discount: current.discount,
        total_amount: current.totalPrice,
        expire_date: current.expDate,
    }, {
        onSuccess: (pageObj) => {
            getItems()
            toast.add({ severity: pageObj.props.flash.severity, summary: pageObj.props.flash.message, life: 2000 });
        }
    })
}

const deleteItem = () => {
    console.log(selectedItem.value)
    if (selectedItem.value) {
        router.delete('/order/item/' + selectedItem.value.id, {
            onSuccess: () => {
                console.log("item deleted")
                getItems()
            }
        })
    } else {
        toast.add({ severity: "error", summary: "Please, select an item to delete" })
    }
}

</script>