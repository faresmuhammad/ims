import {onMounted, ref, reactive, toRefs} from "vue"
import {formatTimeSince, formatExpireDate} from '@/helpers';
import {router} from "@inertiajs/vue3";
import moment from "moment";

const getProduct = (code, target) => {
    const url = target === null ? '/product/' + code : '/product/' + code + '/' + target
    return axios.get(url)
};

export function useOrders(order) {

    const items = ref([])
    const totalOrderPrice = ref(0)
    const getItems = async () => {
        const response = await axios.get('/order/' + order.reference_code + '/items')
        items.value = response.data
        totalOrderPrice.value = calculateTotalOrderPrice(items.value)
        console.log(items.value)
    }


    onMounted(getItems)

    const getNewProduct = async (item, target, onerror) => {
        try {
            const response = (await getProduct(item.code, target));
            const product = response.data;
            const haveStock = 'stock' in product;
            item.product_id = product.id;
            item.name = product.name;
            item.unit_price = haveStock ? 'prices' in product.stock ? product.stock.prices[0].price : product.stock.price : product.price;
            item.expDate = haveStock ? 'expire_dates' in product.stock ? product.stock.expire_dates[0].expire_date : product.stock.expire_date : '';
            item.parts_per_unit = product.parts_per_unit;
            if (haveStock) {
                item.stock = product.stock;
                item.stock_id = 'prices' in product.stock ? product.stock.prices[0].id : product.stock.id;
            }
        } catch (error) {
            onerror(error.response.data.message)
        }
    };
    const submitItem = async (safeToSubmit, item, extraAction = () => {
    }) => {
        if (!safeToSubmit) return;
        const data = {
            order_id: order.id,
            product_id: item.product_id,
            quantity: item.quantity,
            parts: item.parts,
            discount: item.discount,
            discount_limit: item.discount_limit,
            unit_price: item.unit_price,
            total_amount: calculateItemTotalPrice(
                item.unit_price,
                item.quantity,
                item.parts,
                item.parts_per_unit,
                item.discount
            ),
            expire_date: item.expDate
        }
        const dataToSubmit = 'stock_id' in item ? {...data, stock_id: item.stock_id} : data
        const response = await axios.post('/order/' + order.reference_code + '/items', dataToSubmit).catch((error) => console.log(error))

        items.value = response.data.items
        totalOrderPrice.value = calculateTotalOrderPrice(items.value)

        resetForm(item)
        extraAction()
    }


    //TODO: handle customer order item and stock update
    const updateItem = async (event, current, safeToUpdate,target, extraAction = () => {
    },onerror) => {
        if (!safeToUpdate) return;

        if (event.field === "product") {
            try {
                current.code = event.newData.product.code;
                console.log(current);
                const product = (await getProduct(current.code,target)).data;
                current.product_id = product.id;
                current.name = product.name;
                current.unit_price = product.price;
                current.parts_per_unit = product.parts_per_unit;
            } catch (e) {
                onerror(e.response.data.message)
            }
        }

        if (event.field === "quantity") current.quantity = event.newData.quantity;
        if (event.field === "parts") current.parts = event.newData.parts;
        if (event.field === "unit_price")
            current.unit_price = event.newData.unit_price;
        if (event.field === "discount") current.discount = event.newData.discount;
        if ('discount_limit' in current && event.field === "discount_limit")
            current.discount_limit = event.newData.discount_limit;
        if (event.field === "expire_date")
            current.expDate = event.newData.expire_date;


        router.put(
            "/order/item/" + current.id,
            {
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
                stock_id: current.stock_id
            },
            {
                onSuccess: (page) => {
                    getItems();
                    extraAction(page)
                },
                onError: (e) => {
                    console.log(e.response);
                },
            }
        );
    };

    const formattedTimeSince = ref(formatTimeSince(order.created_at))

    const updateTodayTimeSince = () => {

        if (new Date(order.created_at).toDateString() === new Date().toDateString()) {
            setInterval(() => {
                formattedTimeSince.value = formatTimeSince(order.created_at);
            }, 1000 * 60)
        }
    }

    const resetForm = (item) => {
        item.product_id = null;
        item.code = "";
        item.name = "";
        item.quantity = 1;
        item.parts = 0;
        item.discount = 0;
        if ('discount_limit' in item) item.discount_limit = 0;
        item.unit_price = 0;
        item.expDate = "";
        item.stock_id = null
    };
    return {
        items, getItems,
        totalOrderPrice,
        getProduct,
        getNewProduct,
        submitItem,
        updateItem,
        formattedTimeSince,
        updateTodayTimeSince,
        resetForm
    }
}


export function supplierValidator(newItem, currentItem) {
    const errorsNew = reactive({
        code: null,
        discountLimit: null,
        expireDate: {
            severity: "",
            message: null,
        },
    })
    const errorsCurrent = reactive({
        code: null,
        discountLimit: null,
        expireDate: {
            severity: "",
            message: null,
        },
    })

    const validate = (value, field, item) => {
        if (field === 'discount_limit') {
            const message = "Discount limit must not reach to discount value"
            if (item === 'new')
                errorsNew.discountLimit = value > 0 && value >= newItem.discount ? message : null
            else
                errorsCurrent.discountLimit = value > 0 && value >= currentItem.discount ? message : null

        }
        if (field === 'discount') {
            const message = "Discount limit must not reach to discount value"
            if (item === 'new')
                errorsNew.discountLimit = newItem.discount_limit > 0 && newItem.discount_limit >= value ? message : null;
            else
                errorsCurrent.discountLimit = currentItem.discount_limit > 0 && currentItem.discount_limit >= value ? message : null;
        }
        if (field === 'expDate') {
            const pastDateMessage = "Expire date must be greater than today"
            const expireSoonMessage = "Expire Date will be reached in less than 6 months"
            const date = moment(value, 'MM/YYYY', true)
            if (date.isValid()) {
                if (item === 'new')
                    errorsNew.expireDate = date < moment() ? {
                            message: pastDateMessage,
                            severity: "error"
                        }
                        : date < moment().add(6, 'months') ? {
                            message: expireSoonMessage,
                            severity: "warn"
                        } : {message: null, severity: ""};
                else
                    errorsCurrent.expireDate = date < moment() ? {
                            message: pastDateMessage,
                            severity: "error"
                        }
                        : date < moment().add(6, 'months') ? {
                            message: expireSoonMessage,
                            severity: "warn"
                        } : {message: null, severity: ""};
            } else {
                //FIXME: restrict year range
                errorsNew.expireDate = {
                    message: "Invalid date",
                    severity: "error"
                }

            }
        }
    }

    const validateProduct = async (code, type) => {
        type === 'new' ? errorsNew.code = null : errorsCurrent.code = null
        try {
            (await getProduct(code, 'supplier'))
        } catch (e) {
            if (type === 'new')
                errorsNew.code = e.response.data.message
            else
                errorsCurrent.code = e.response.data.message
        }
    }


    return {
        errorsCurrent, errorsNew, validate, validateProduct
    }
}

export function customerValidator(newItem, currentItem) {
    /**Fields to validate
     * code -> product or sock exist
     * quantity -> exceed the available quantity
     *
     */
}

export function calculateItemTotalPrice(price, quantity, parts = 0, partsPerUnit = 1, discount = 0) {
    const discountDecimal = discount / 100;
    let totalPrice = 0;
    if (parts === 0)
        totalPrice = (price * quantity) * (1 - discountDecimal);
    else {
        const partsPerUnitChecked = partsPerUnit == null ? 1 : partsPerUnit
        totalPrice = (price * quantity + (price / partsPerUnitChecked) * parts) * (1 - discountDecimal);
    }


    return totalPrice;
}


export function calculateTotalOrderPrice(items) {
    let total = 0;
    items.forEach((item) => {
        total += item.total_amount
    })
    return total
}
