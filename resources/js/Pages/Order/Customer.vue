<template>
    <Head :title="order ? 'Order #' + order.reference_code : 'No Order'"/>
    <div class="card m-3">
        <order-header title="Customer" :order="order"/>

        <DataTable
            v-model:selection="selectedItem"
            selectionMode="single"
            :value="items"
            stripedRows
            showGridlines
            editMode="cell"
            @cell-edit-complete="updateCurrentItem"
            @cell-edit-init="beginEdit"
            @keyup.ctrl.delete.exact="deleteItem"
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
                    <InputText v-model="data[field].code"/>
                </template>
            </Column>
            <Column
                field="product.name"
                header="Name"
                class="text-center col-3"
            ></Column>
            <Column
                field="quantity"
                header="Quantity"
                class="text-center col-1"
            >
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputmode="integer"/>
                </template>
            </Column>
            <Column field="parts" header="Parts" class="text-center col-1">
                <template #body="{ field, data }">
                    {{ data[field] }}
                </template>
                <template #editor="{ field, data }">
                    <InputNumber v-model="data[field]" inputId="integer"/>
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
                <template #editor="{ data, field }">
                    <InputNumber
                        v-model="data[field]"
                        mode="currency"
                        currency="EGP"
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
                <template #editor="{ field, data }">
                    <InputNumber
                        v-model="data[field]"
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
                <template #editor="{ field, data }">
                    <div class="flex flex-column">
                        <InputMask
                            id="basic"
                            v-model="data[field]"
                            placeholder="02/2025"
                            mask="99/9999"
                            slotChar="mm/yyyy"
                        />
                        >
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

            <template #footer>
                <div
                    @keydown.ctrl.enter="submitNewItem"
                    @keyup.enter.exact="getNewProduct(newItem,'customer')"
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
                                <!-- TODO: implement validation -->
                                <!-- :invalid="errorsNew.code !== null"
                                <small class="p-error">{{
                                    errorsNew.code
                                }}</small> -->
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
                                <!-- TODO: track user input, if it exceeded the quantity in stock show a warning popup-->
                                <label for="quantity">Quantity</label>
                                <InputNumber
                                    v-model="newItem.quantity"
                                    inputClass="w-full"
                                    id="quantity"
                                    inputId="integer"
                                />
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
                                <!-- TODO:suggestion: show list of prices for available stocks if there are different prices -->
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
                            <!-- TODO: validate not to exceed stock discount limit -->
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
                                />
                                <!-- TODO: implement validation -->
                                <!-- @input="
                                        checkValidItem($event.value, 'discount')
                                    " -->
                            </div>
                        </div>
                        <div class="field col-1">
                            <!-- TODO:suggestion: if product code entered, show dropdown with list of available stocks by expire date (if different expiredates) and available quantity and parts  -->
                            <!-- TODO:suggestion: if stock code entered, show the same above with selected expire date -->
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
                                <!-- TODO: implement validation -->
                                <!-- @update:modelValue="
                                        checkValidItem($event, 'expDate')
                                    "
                                    :invalid="errorsNew.expireDate.message" -->
                                <!-- <small
                                    v-if="errorsNew.expireDate"
                                    :class="
                                        errorsNew.expireDate.severity ===
                                        'error'
                                            ? 'p-error'
                                            : 'text-yellow-600'
                                    "
                                    >{{ errorsNew.expireDate.message }}</small
                                > -->
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
});

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
