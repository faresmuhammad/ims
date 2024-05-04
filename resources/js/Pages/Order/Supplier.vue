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
        <Tag :value="order.completed ? 'Completed' : 'Incomplete'" :severity="order.completed ? 'success' : 'warning'" class="text-xl py-1 px-3"
            :icon="order.completed ? 'pi pi-check text-xl' : 'pi pi-times text-xl'"  />
        <!-- Table of order items -->
        <Toast position="top-center" group="tc" />
        <DataTable v-model:selection="selectedItem" selectionMode="single" :value="items" stripedRows showGridlines
            editMode="cell" @cell-edit-complete="updateItem" @cell-edit-init="beginEdit"
            @keyup.ctrl.delete.exact="deleteItem">
            <template #header>
                <div class="flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="text-xl text-900 font-bold">Supplier</span>
                </div>
            </template>

            <Column field="product" key="product.code" header="Code" class="text-center col">
                <template #body="{ field, data }">
                    {{ data[field].code }}
                </template>
                <template #editor="{ field, data }">
                    <InputText v-model="data[field].code"/>
                </template>
            </Column>
            <Column field="product.name" header="Name" class="text-center col-3"></Column>
            <Column field="quantity" header="Quantity" class="text-center col-1">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputmode="integer" />
                </template>
            </Column>
            <Column field="parts" header="Parts" class="text-center col-1">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputId="integer" />
                </template>
            </Column>
            <Column field="unit_price" header="Selling Price" class="text-center col-1">
                <template #body={data,field}>
                    EGP {{data[field]}}
                </template>
                <template #editor={data,field}>
                    <InputNumber v-model="data[field]" mode="currency" currency="EGP"/>
                </template>
            </Column>
            <Column field="discount" header="Discount" class="text-center col-1">
                <template #body="{ field, data }">
                    %{{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputId="percent" prefix="%" :min="0" :max="100" @input="checkValidCurrentItem($event.value,'discount')"/>
                </template>
            </Column>
            <Column field="discount_limit" header="Discount Limit" class="text-center col-1">
                <template #body="{ field, data }">
                    %{{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <div class="flex flex-column">
                    <InputNumber v-model="data[field]" inputId="percent" prefix="%" :min="0" :max="100" @input="checkValidCurrentItem($event.value,'discount_limit')"/>
                    <small class="p-error">{{errorsCurrent.discountLimit}}</small>
                    </div>
                </template>
            </Column>
            <Column field="expire_date" header="Exp. Date" class="text-center col-2">
                <template #body="{ field, data }">
                    {{ data[field] ? data[field] : 'Empty Exp. Date' }}
                </template>
                <template #editor="{ field, data }">
                    <div class="flex flex-column">

                    <InputMask id="basic" v-model="data[field]" placeholder="02/2025" mask="99/9999"
                        slotChar="mm/yyyy" @update:modelValue="checkValidCurrentItem($event,'expDate')"/>
                    <small v-if="errorsCurrent.expireDate" :class="errorsCurrent.expireDate.severity === 'error' ? 'p-error':'text-yellow-600'">{{errorsCurrent.expireDate.message}}</small>
                    </div>
                </template>
            </Column>
            <Column field="total_amount" header="Total Price" class="text-center col-1">
            <template #body={data,field}>
                EGP {{data[field].toFixed(2)}}
            </template>
            </Column>


            <template #footer>
                <div @keydown.ctrl.enter="submitNewItem" @keyup.enter.exact="getNewProduct" class="card p-3"
                    style="border-color: var(--primary-color)">

                    <div class="formgrid grid">
                        <!-- feedback: product not found -->
                        <div class="field col-2">
                            <div class="flex flex-column gap-2">

                            <label for="code">Code</label>
                            <InputText v-model="newItem.code" class="w-full" id="code" :invalid="errorsNew.code !== null"/>
                            <small class="p-error">{{errorsNew.code}}</small>
                        </div>
                            </div>
                        <div class="field col-3">
                            <div class="flex flex-column gap-2">

                            <label for="name">Name</label>
                            <InputText v-model="newItem.name" class="w-full" id="name" disabled />
                            </div>
                        </div>
                        <div class="field col-1 m-0">
                            <div class="flex flex-column gap-2">

                            <label for="quantity">Quantity</label>
                            <InputNumber v-model="newItem.quantity" inputClass="w-full" id="quantity" inputId="integer" />
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">

                            <label for="parts">Parts</label>
                            <InputNumber v-model="newItem.parts" inputClass="w-full" id="parts" inputId="integer" />
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">

                            <label for="selling-price">Selling Price</label>
                            <InputNumber v-model="newItem.unit_price" inputClass="w-full" id="selling-price" mode="currency" currency="EGP"/>
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">

                            <label for="discount">Discount</label>
                            <InputNumber v-model="newItem.discount" inputClass="w-full" id="discount" inputId="percent"
                                prefix="%" :min="0" :max="100" @input="checkValidItem($event.value,'discount')"/>
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">

                            <label for="discount_limit">Discount Limit</label>
                            <InputNumber v-model="newItem.discount_limit" inputClass="w-full" id="discount_limit" inputId="percent"
                                prefix="%" :min="0" :max="100" @input="checkValidItem($event.value,'discount_limit')" :invalid="errorsNew.discountLimit !== null"/>
                            <small class="p-error">{{errorsNew.discountLimit}}</small>
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">

                            <label for="exp-date">Exp. Date</label>
                            <InputMask id="exp-date" v-model="newItem.expDate" placeholder="02/2025" mask="99/9999"
                                slotChar="mm/yyyy" inputClass="w-full" @update:modelValue="checkValidItem($event,'expDate')" :invalid="errorsNew.expireDate != null"/>
                                <small v-if="errorsNew.expireDate" :class="errorsNew.expireDate.severity === 'error' ? 'p-error':'text-yellow-600'">{{errorsNew.expireDate.message}}</small>
                            </div>
                        </div>
                        <!-- <div class="field col-1">
                            <label for="total-price">Total Price</label>
                            <InputText v-model="newItem.totalPrice" class="w-full" id="total-price" />
                        </div> -->
                    </div>
                </div>
            </template>
        </DataTable>
        <!-- Total Price -->
        <h5>Total Price: {{totalOrderPrice}}</h5>
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
import { useOrders,calculateItemTotalPrice } from '@/composables/orders';
import { formatDateTime } from '@/helpers';

const props = defineProps({
    order: Object,
    suppliers: Object,
})

const {items, getItems, 
    totalOrderPrice,
     getProduct,
     submitItem,
     formattedTimeSince,
     updateTodayTimeSince
    } = useOrders(props.order)
const toast = useToast();

updateTodayTimeSince()

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
    discount_limit: 0,
    unit_price: 0,
    parts_per_unit: null,
    expDate: ''
})

const current = reactive({
    id: null,
    product_id: null,
    code: '',
    name: '',
    quantity: 1,
    parts: 0,
    discount: 0,
    discount_limit: 0,
    unit_price: 0,
    parts_per_unit: null,
    totalPrice: 0,
    expDate: ''
})

const errorsNew = reactive({
    code: null,
    discountLimit:null,
    expireDate:{
        severity: 'error',
        message: null
    }
})

const errorsCurrent = reactive({
    code: null,
    discountLimit:null,
    expireDate:{
        severity: 'error',
        message: null
    },
})





//FIXME: fix when discount equal zero
const checkValidItem = (value,field)=>{
    if(field === 'discount_limit')
        errorsNew.discountLimit = value >= newItem.discount ? 'Discount limit must not reach to discount value' : null
    else if(field === 'discount')
        errorsNew.discountLimit = newItem.discount_limit >= value ? 'Discount limit must not reach to discount value' : null
    else if(field === 'expDate')
        {
            const dateParts = value.split('/')
            console.log(dateParts);
            const date = new Date(Number(dateParts[1]),Number(dateParts[0]) -1,1);
            //TODO: get the date offset from settings
            errorsNew.expireDate = date < new Date() ? {severity: 'error', message: 'Expire date must be greater than today'} : date < new Date().setMonth(new Date().getMonth() + 6) ? {severity: 'warn', message: 'Expire Date will be reached in less than 6 months' } : null
        }
    console.log(errorsNew);

}
//FIXME: fix when discount equal zero
const checkValidCurrentItem = (value,field)=>{
    if(field === 'discount_limit')
        errorsCurrent.discountLimit = value >= current.discount ? 'Discount limit must not reach to discount value' : null
    else if(field === 'discount')
        errorsCurrent.discountLimit = current.discount_limit >= value ? 'Discount limit must not reach to discount value' : null
    else if(field === 'expDate')
        {
            const dateParts = value.split('/')
            console.log(dateParts);
            const date = new Date(Number(dateParts[1]),Number(dateParts[0]) - 1, 1);
            //TODO: get the date offset from settings
            errorsCurrent.expireDate = date < new Date() ? {severity: 'error', message: 'Expire date must be greater than today'} : date < new Date().setMonth(new Date().getMonth() + 6) ? {severity: 'warn', message: 'Expire Date will be reached in less than 6 months' }: null
        }
    console.log(errorsCurrent);

}



const getNewProduct = async ()=>{
    const product = (await getProduct(newItem.code)).data
    newItem.product_id = product.id
    newItem.name = product.name
    newItem.unit_price = product.price
    newItem.parts_per_unit = product.parts_per_unit
}


const submitNewItem = () => {
    const safeToSubmit = errorsNew.code || errorsNew.discountLimit || errorsNew.expireDate.message ? false : true
    console.log(safeToSubmit);
    submitItem(safeToSubmit,newItem,resetForm)
}

const resetForm = () => {
    newItem.product_id = null
    newItem.code = ''
    newItem.name = ''
    newItem.quantity = 1
    newItem.parts = 0
    newItem.discount = 0
    newItem.discount_limit = 0
    newItem.unit_price = 0
    newItem.expDate = ''
}

const beginEdit = (event) => {
    errorsCurrent.code = null
    errorsCurrent.discountLimit = null
    errorsCurrent.expireDate.message = null
    const item = event.data
        current.id = item.id,
        current.product_id = item.product.id,
        current.code = item.product.code,
        current.name = item.product.name,
        current.quantity = item.quantity,
        current.parts = item.parts,
        current.discount = item.discount,
        current.discount_limit = item.discount_limit,
        current.unit_price = item.unit_price,
        current.parts_per_unit = item.parts_per_unit,
        current.totalPrice = item.total_amount,
        current.expDate = item.expire_date
}

const updateItem = async (event) => {

    if (event.field === 'product') {
        current.code = event.newData.product.code
        console.log(current);
        const product = (await getProduct(current.code)).data
    current.product_id = product.id
    current.name = product.name
    current.unit_price = product.price
    current.parts_per_unit = product.parts_per_unit
        console.log(current);
    }
    
    if (event.field === 'quantity') current.quantity = event.newData.quantity;
    if (event.field === 'parts') current.parts = event.newData.parts;
    if (event.field === 'unit_price') current.unit_price = event.newData.unit_price;
    if (event.field === 'discount') current.discount = event.newData.discount;
    if (event.field === 'discount_limit') current.discount_limit = event.newData.discount_limit;
    if (event.field === 'expire_date') current.expDate = event.newData.expire_date;
    
    if(errorsCurrent.code || errorsCurrent.discountLimit || errorsCurrent.expireDate.message) return;
    console.log(current)
    router.put('/order/item/' + current.id, {
        product_id: current.product_id,
        quantity: current.quantity,
        parts: current.parts,
        unit_price: current.unit_price,
        discount: current.discount,
        discount_limit: current.discount_limit,
        total_amount: calculateItemTotalPrice(
            current.unit_price,
            current.quantity,
            current.parts,
            current.parts_per_unit,
            current.discount
        ),
        expire_date: current.expDate,
    }, {
        onSuccess: (pageObj) => {
            getItems()
            toast.add({ severity: pageObj.props.flash.severity, summary: pageObj.props.flash.message, life: 2000 });
        },
        onError:(e)=>{
            console.log(e.response);
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
            discount_limit: item.discount, 
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




</script>