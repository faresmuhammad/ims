<template>

    <Head :title="order ? 'Order #' + order.reference_code : 'No Order'" />
    <div class="card m-3">
        <!-- Header -->
        <Toast />
        <div class="flex justify-content-between">

            <h2>Supplier</h2>
            <Dropdown v-model="selectedSupplier" :options="suppliers" placeholder="Select a Supplier"
                @update:modelValue="updateSupplierOfOrder" optionLabel="name" optionValue="id" />
            <h5>{{ order ? 'Order #' + order.reference_code : 'No Order' }}</h5>

            <!-- Show created since [time] -->
            <!--  -->
            <span>Created at: <Inplace>
                    <template #display>
                        {{ formattedTimeSince }}
                        {{ console.log(formattedTimeSince) }}
                    </template>
                    <template #content>

                        {{ formatDateTime(order.created_at) }}
                    </template>
                </Inplace></span>
            <!-- Show updated since [time] -->
        </div>
        <Tag :value="order.completed ? 'Completed' : 'Incomplete'" :severity="order.completed ? 'success' : 'warning'"
            :icon="order.completed ? 'pi pi-check' : 'pi pi-times'" />
        <!-- Table of order items -->
        <DataTable v-model:selection="selectedItem" selectionMode="single" :value="items" stripedRows showGridlines
            editMode="cell" @cell-edit-complete="updateItem" @cell-edit-init="beginEdit"
            @keyup.ctrl.delete.exact="deleteItem">
            <template #header>
                <div class="flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="text-xl text-900 font-bold">Supplier</span>
                </div>
            </template>

            <Column field="product" key="product.code" header="Code" class="col">
                <template #body="{ field, data }">
                    {{ data[field].code }}
                </template>
                <template #editor="{ field, data }">
                    <InputText v-model="data[field]" />
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
            <Column field="product.price" header="Selling Price" class="col-1"></Column>
            <Column field="discount" header="Discount" class="col-1">
                <template #body="{ field, data }">
                    %{{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputId="percent" prefix="%" :min="0" :max="100" />
                </template>
            </Column>
            <Column field="expire_date" header="Exp. Date" class="col-2">
                <template #body="{ field, data }">
                    {{ data[field] ? data[field] : 'Empty Exp. Date' }}
                </template>
                <template #editor="{ field, data }">
                    <InputMask id="basic" v-model="data[field]" placeholder="02/2025" mask="99/9999"
                        slotChar="mm/yyyy" />
                </template>
            </Column>
            <Column field="total_price" header="Total Price" class="col-1"></Column>


            <template #footer>
                <div @keyup.ctrl.enter="submitItem" @keyup.enter.exact="getProduct('new')" class="card p-3"
                    style="border-color: var(--primary-color)">

                    <div class="formgrid grid">
                        <!-- feedback: product not found -->
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
                            <label for="selling-price">Selling Price</label>
                            <InputText v-model="newItem.unit_price" class="w-full" id="selling-price" />
                        </div>
                        <div class="field col-1">
                            <label for="discount">Discount</label>
                            <InputNumber v-model="newItem.discount" class="w-full" id="discount" inputId="percent"
                                prefix="%" :min="0" :max="100" />
                        </div>
                        <div class="field col-2">
                            <!-- feedback: prevent past -->
                            <!-- feedback: warn if it's near -->
                            <label for="exp-date">Exp. Date</label>
                            <InputMask id="basic" v-model="newItem.expDate" placeholder="02/2025" mask="99/9999"
                                slotChar="mm/yyyy" />
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
        <div :class="order.completed ? 'hidden' : 'flex justify-content-end'">
            <Button label="Cancel" outlined severity="danger" />
            <Button label="Save" class="mx-2" severity="success" @click="completeOrder" />
        </div>
    </div>
</template>


<script setup>
import { Head, router } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
const props = defineProps({
    order: Object,
    suppliers: Object,
})
const toast = useToast();

const selectedSupplier = ref(props.order.supplier_id)

const updateSupplierOfOrder = () => {
    router.put('/orders/' + props.order.id, {
        supplier_id: selectedSupplier.value
    }, {
        onSuccess: (page) => {
            console.log(page)
        }
    })
}
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
//FIXME: function not called
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
        product_id: item.product.id,
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

    console.log(current.id)
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
    if (event.field === 'expire_date') current.expDate = event.newData.expire_date;


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

const completeOrder = () => {
    //loop over the items
    const stocks = []
    items.value.forEach(item => {
        //prepare stock object
        const stock = {
            product_id: item.product.id,
            supplier_id: selectedSupplier.value,
            quantity: item.quantity,
            parts: item.parts,
            price: item.unit_price,
            discount: item.discount,
            discount_limit: item.discount, //TODO: add discount limit input
            expire_date: item.expire_date
        }

        stocks.push(stock)
    });
    //TODO:: handle validation flash errors
    axios.post('/stocks', {
        stocks: stocks,
        order_id: props.order.id
    }).then((res) => {
        console.log("stocks added", res.data);

    }).catch((error) => {

        // console.log(error);
    })
    //send create stock requests
    //update order to completed
}

const formatTimeSince = (datetime) => {

    const date = new Date(datetime);
    const diff = new Date() - date
    const minutes = Math.floor(diff / (1000 * 60))
    const hours = Math.floor(diff / (1000 * 60 * 60))
    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    const weeks = Math.floor(diff / (1000 * 60 * 60 * 24 * 7))
    const months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30))
    const years = Math.floor(diff / (1000 * 60 * 60 * 24 * 365))
    const formatted = years < 1 ? months < 1 ? weeks < 1 ? days == 1 ? 'Yesterday' : days < 1 ? hours < 1 ? minutes < 1 ? 'Just Now' : `${minutes} minutes ago` : `${hours} hours ago` : `${days} days ago` : `${weeks} weeks ago` : `${months} months ago` : `${years} years ago`;
    return formatted;
}

const formatDateTime = (datetime) => {
    const date = new Date(datetime)
    return date.toLocaleString()
}
const formattedTimeSince = ref(formatTimeSince(props.order.created_at))

if (new Date(props.order.created_at).toDateString() === new Date().toDateString()) {

    setInterval(() => {
        formattedTimeSince.value = formatTimeSince(props.order.created_at);
        console.log(formattedTimeSince.value)
    }, 1000 * 60)
} else {

    formattedTimeSince.value = formatTimeSince(props.order.created_at);
}
</script>