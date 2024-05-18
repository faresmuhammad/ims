<template>
    <Head :title="order ? 'Order #' + order.reference_code : 'No Order'"/>
    <div class="card m-3">
        <order-header title="Customer" :order="order"/>

        <DataTable
            v-model:selection="selectedItem"
            v-model:editingRows="editingRows"
            selectionMode="single"
            :value="items"
            stripedRows
            showGridlines
            editMode="row"
            @row-edit-save="updateCurrentItem"
            @row-edit-init="beginEdit"
            @keyup.ctrl.delete.exact="deleteItem"
            dataKey="id"
        >
            <Column
                field="product"
                key="product.code"
                header="Code"
                class="text-center col"
            >
                <template #body="{ field, data }">
                    {{ data[field].code }}
                </template>
                <template #editor="{ field, data }">
                    <InputText v-model="current.code" @keyup.enter.exact="getNewProduct(current,'customer')"/>
                </template>
            </Column>
            <Column
                field="product.name"
                header="Name"
                class="text-center col-3"
            >
                <template #editor="{ field, data }">
                    {{ current.name ?? data[field].name }}
                </template>
            </Column>
            <Column
                field="quantity"
                header="Quantity"
                class="text-center col-1"
            >
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor>
                    <InputNumber v-model="current.quantity" inputmode="integer"/>
                </template>
            </Column>
            <Column field="parts" header="Parts" class="text-center col-1">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor>
                    <InputNumber v-model="current.parts" inputId="integer"/>
                </template>
            </Column>
            <Column
                field="unit_price"
                header="Selling Price"
                class="text-center col-1"
            >
                <template #body="{ data, field }">
                    EGP {{ data[field] }}
                </template>
                <template #editor>
                    <Dropdown
                        v-if="'prices' in current.stock"
                        v-model="current.stock_id"
                        :options="current.stock.prices"
                        optionLabel="price"
                        optionValue="id"
                        @change="setStockIdForItem"
                    />
                    <InputNumber
                        v-else
                        v-model="current.unit_price"
                        inputClass="w-full"
                        id="selling-price"
                        mode="currency"
                        currency="EGP"
                        readonly
                    />
                </template>
            </Column>
            <Column
                field="discount"
                header="Discount"
                class="text-center col-1"
            >
                <template #body="{ field, data }">
                    %{{ data[field] }}
                </template>
                <template #editor>
                    <InputNumber
                        v-model="current.discount"
                        inputId="percent"
                        prefix="%"
                        :min="0"
                        :max="100"
                    />
                    <!-- TODO: implement validation-->
                    <!-- @input="checkValidCurrentItem($event.value, 'discount')" -->
                </template>
            </Column>
            <Column
                field="expire_date"
                header="Exp. Date"
                class="text-center col-2"
            >
                <template #body="{ field, data }">
                    {{
                        data[field]
                            ? formatExpireDate(data[field])
                            : "Empty Exp. Date"
                    }}
                </template>
                <!-- TODO:suggestion: on edit mode show dropdown with list of available stocks by expire date and available quantity and parts  -->
                <template #editor>
                    <div class="flex flex-column">
                        <Dropdown
                            v-if="'expire_dates' in current.stock"
                            v-model="current.stock_id"
                            :options="current.stock.expire_dates"
                            optionLabel="expire_date"
                            optionValue="id"
                        />
                        <InputMask
                            v-else
                            id="exp-date"
                            v-model="current.expDate"
                            mask="99/9999"
                            slotChar="mm/yyyy"
                            placeholder="02/2025"
                            inputClass="w-full"
                            readonly
                        />

                    </div>
                </template>
            </Column>
            <Column
                field="total_amount"
                header="Total Price"
                class="text-center col-1"
            >
                <template #body="{ data, field }">
                    EGP {{ data[field].toFixed(2) }}
                </template>
            </Column>
            <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center"></Column>
            <template #footer>
                <div
                    @keydown.ctrl.enter="submitNewItem"
                    @keyup.enter.exact="errorsNew.code = null;getNewProduct(newItem,'customer',(message)=>{
                        errorsNew.code = message
                    })"
                    class="card p-3"
                    style="border-color: var(--primary-color)"
                >
                    <div class="formgrid grid">
                        <!-- feedback: product not found -->
                        <div class="field col-2">
                            <div class="flex flex-column gap-2">
                                <label for="code">Code</label>
                                <InputText
                                    v-model="newItem.code"
                                    class="w-full"
                                    id="code"
                                />
                                <small class="p-error">{{ errorsNew.code }}</small>
                            </div>
                        </div>
                        <div class="field col-3">
                            <div class="flex flex-column gap-2">
                                <label for="name">Name</label>
                                <InputText
                                    v-model="newItem.name"
                                    class="w-full"
                                    id="name"
                                    readonly
                                />
                            </div>
                        </div>
                        <div class="field col-1 m-0">
                            <div class="flex flex-column gap-2">
                                <label for="quantity">Quantity</label>
                                <InputNumber
                                    v-model="newItem.quantity"
                                    inputClass="w-full"
                                    id="quantity"
                                    inputId="integer"
                                    @input="event => {
                                        if(Object.keys(newItem.stock).length > 0){
                                            if('meta' in newItem.stock){
                                                const selectedStock = newItem.stock.meta.find(stock => stock.id === newItem.stock_id)
                                                const message = 'Available quantity is ' + selectedStock.available_quantity
                                                errorsNew.quantity = event.value > selectedStock.available_quantity ? message : null
                                            }else{
                                                const message = 'Available quantity is ' + newItem.stock.available_quantity
                                                errorsNew.quantity = event.value > newItem.stock.available_quantity ? message : null
                                            }
                                        }
                                    }"
                                />
                                <small class="p-error">{{ errorsNew.quantity }}</small>
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="parts">Parts</label>
                                <!-- TODO: track user input, if it exceeded the parts per unit show a warning popup with action to add the number of parts per unit to quantity and set to parts the remaining -->
                                <InputNumber
                                    v-model="newItem.parts"
                                    inputClass="w-full"
                                    id="parts"
                                    inputId="integer"
                                />
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="selling-price">Selling Price</label>
                                <Dropdown
                                    v-if="'prices' in newItem.stock"
                                    v-model="newItem.stock_id"
                                    :options="newItem.stock.prices"
                                    optionLabel="price"
                                    optionValue="id"
                                    @change="setStockIdForItem"
                                />
                                <InputNumber
                                    v-else
                                    v-model="newItem.unit_price"
                                    inputClass="w-full"
                                    id="selling-price"
                                    mode="currency"
                                    currency="EGP"
                                    readonly
                                />


                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="discount">Discount</label>
                                <InputNumber
                                    v-model="newItem.discount"
                                    inputClass="w-full"
                                    id="discount"
                                    inputId="percent"
                                    prefix="%"
                                    :min="0"
                                    :max="100"
                                    @input="event => {
                                            if(Object.keys(newItem.stock).length > 0){
                                                if('meta' in newItem.stock){
                                                    const selectedStock = newItem.stock.meta.find(stock => stock.id === newItem.stock_id)
                                                    const message = 'Discount mustn\'t exceed discount limit -> ' + selectedStock.discount_limit
                                                    errorsNew.discount = event.value > selectedStock.discount_limit ? message : null
                                                }else{
                                                    const message = 'Discount mustn\'t exceed discount limit -> ' + newItem.stock.discount_limit
                                                    errorsNew.discount = event.value > newItem.stock.discount_limit ? message : null
                                                }
                                        }

                                    }"
                                />
                                <small class="p-error">{{ errorsNew.discount }}</small>
                            </div>
                        </div>
                        <div class="field col-1">
                            <div class="flex flex-column gap-2">
                                <label for="exp-date">Exp. Date</label>
                                <Dropdown
                                    v-if="'expire_dates' in newItem.stock"
                                    v-model="newItem.stock_id"
                                    :options="newItem.stock.expire_dates"
                                    optionLabel="expire_date"
                                    optionValue="id"
                                    @change="setStockIdForItem"
                                />
                                <InputMask
                                    v-else
                                    id="exp-date"
                                    v-model="newItem.expDate"
                                    mask="99/9999"
                                    slotChar="mm/yyyy"
                                    placeholder="02/2025"
                                    inputClass="w-full"
                                    readonly
                                />

                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </DataTable>
        <h5>Total Price: {{ totalOrderPrice }}</h5>
        <!-- Save and cancel buttons -->
        <div :class="order.completed ? 'hidden' : 'flex justify-content-end'">
            <Button label="Cancel" outlined severity="danger"/>
            <Button
                label="Save"
                class="mx-2"
                severity="success"
                @click="completeOrder"
            />
        </div>
    </div>
</template>

<script setup>
import {Head, router} from "@inertiajs/vue3";
import {ref, reactive} from "vue";
import OrderHeader from "@/Components/OrderHeader.vue";
import {useOrders} from "@/composables/orders";
import {formatExpireDate} from "@/helpers";

const props = defineProps({
    order: Object,
});

const {
    items,
    getItems,
    totalOrderPrice,
    getNewProduct,
    submitItem,
    updateItem,
} = useOrders(props.order);

const selectedItem = ref();
const editingRows = ref([])

const newItem = reactive({
    product_id: null,
    code: "",
    name: "",
    quantity: 1,
    parts: 0,
    discount: 0,
    unit_price: 0,
    parts_per_unit: null,
    expDate: "",
    stock: {},
    stock_id: null,
});

const current = reactive({
    id: null,
    product_id: null,
    code: "",
    name: "",
    quantity: 1,
    parts: 0,
    discount: 0,
    unit_price: 0,
    parts_per_unit: null,
    totalPrice: 0,
    expDate: "",
    stock: {},
    stock_id: null,
});

const errorsNew = reactive({
    code: null,
    quantity: null,
    discount: null
})
const errorsCurrent = reactive({
    code: null,
    quantity: null,
    discount: null
})

const setStockIdForItem = (event) => {
    newItem.unit_price = newItem.stock.prices.find(
        price => price.id === event.value
    ).price;
    newItem.expDate = newItem.stock.expire_dates.find(
        date => date.id === event.value
    ).expire_date;
};

const submitNewItem = () => {
    const safeToSubmit = true; //TODO: add validation
    submitItem(safeToSubmit, newItem);
};

const beginEdit = (event) => {
    //TODO: clear errors

    const item = event.data;
    current.id = item.id;
    current.product_id = item.product.id;
    current.code = item.product.code;
    current.name = item.product.name;
    current.quantity = item.quantity;
    current.parts = item.parts;
    current.discount = item.discount;
    current.discount_limit = item.discount_limit;
    current.unit_price = item.unit_price;
    current.parts_per_unit = item.parts_per_unit;
    current.totalPrice = item.total_amount;
    current.expDate = item.expire_date;
    current.stock = {}
    current.stock_id = item.stock_id
};

const updateCurrentItem = (event) => {
    const safeToUpdate = true; //TODO: add validation
    updateItem(event, current, safeToUpdate, (page) => {
        toast.add({
            severity: page.props.flash.severity,
            summary: page.props.flash.message,
            life: 2000,
        });
    });
};

const completeOrder = () => {
    console.log(items.value)
    router.put('/order/customer/complete', {
            order_id: props.order.id,
            items: items.value
        },
        {
            onSuccess: (page) => {
                console.log(page)
            }
        })
};

const deleteItem = () => {
    console.log(selectedItem.value);
    if (selectedItem.value) {
        router.delete("/order/item/" + selectedItem.value.id, {
            onSuccess: () => {
                console.log("item deleted");
                getItems();
            },
        });
    } else {
        toast.add({
            severity: "error",
            summary: "Please, select an item to delete",
        });
    }
};
</script>
