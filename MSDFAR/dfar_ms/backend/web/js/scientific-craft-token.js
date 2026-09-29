(function (window, document, $) {
    'use strict';

    if (!$) {
        throw new Error(
            'scientific-craft-token.js requires jQuery.'
        );
    }

    const UUID_PATTERN =
        /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;

    const DEFAULT_ROUTES = {
        addSampleCraft:
            '/scientific/addsamplecraft',

        catchData:
            '/scientific/catchdata',

        addLengthWeight:
            '/scientific/addlengthweight',

        addOperationCost:
            '/scientific/addoperationcost',

        create:
            '/scientific/create'
    };

    let publishedContextToken = '';
    let dropdownTimer = null;

    function createToken() {
        if (
            window.crypto &&
            typeof window.crypto.randomUUID ===
                'function'
        ) {
            return window.crypto.randomUUID();
        }

        if (
            !window.crypto ||
            typeof window.crypto.getRandomValues !==
                'function'
        ) {
            throw new Error(
                'Secure random token generation is unavailable.'
            );
        }

        const bytes =
            new Uint8Array(16);

        window.crypto.getRandomValues(
            bytes
        );

        /*
         * RFC 4122 version 4 UUID.
         */
        bytes[6] =
            (bytes[6] & 0x0f) | 0x40;

        bytes[8] =
            (bytes[8] & 0x3f) | 0x80;

        const hex = Array.from(
            bytes,
            function (byte) {
                return byte
                    .toString(16)
                    .padStart(2, '0');
            }
        );

        return [
            hex.slice(0, 4).join(''),
            hex.slice(4, 6).join(''),
            hex.slice(6, 8).join(''),
            hex.slice(8, 10).join(''),
            hex.slice(10, 16).join('')
        ].join('-');
    }

    function storageKey() {
        return String(
            window.scientificStorageKey ||
            'scientific'
        );
    }

    function getScientificObject() {
        let scientificObject = null;

        /*
         * Supports:
         *
         * let scientific = {...};
         *
         * and:
         *
         * window.scientific = {...};
         */
        try {
            if (
                typeof scientific !==
                    'undefined' &&
                scientific &&
                typeof scientific ===
                    'object'
            ) {
                scientificObject =
                    scientific;
            }
        } catch (error) {
            scientificObject = null;
        }

        if (
            !scientificObject &&
            window.scientific &&
            typeof window.scientific ===
                'object'
        ) {
            scientificObject =
                window.scientific;
        }

        /*
         * Restore the draft when the main
         * Scientific script has not created
         * the object yet.
         */
        if (!scientificObject) {
            try {
                const storedDraft =
                    window.localStorage.getItem(
                        storageKey()
                    );

                if (storedDraft) {
                    const parsedDraft =
                        JSON.parse(
                            storedDraft
                        );

                    if (
                        parsedDraft &&
                        typeof parsedDraft ===
                            'object'
                    ) {
                        window.scientific =
                            parsedDraft;

                        scientificObject =
                            parsedDraft;
                    }
                }
            } catch (error) {
                console.warn(
                    'Unable to restore the scientific draft.',
                    error
                );
            }
        }

        if (!scientificObject) {
            return null;
        }

        if (
            !Array.isArray(
                scientificObject.craft
            )
        ) {
            scientificObject.craft = [];
        }

        return scientificObject;
    }

    function persistScientific() {
        const scientificObject =
            getScientificObject();

        if (!scientificObject) {
            return;
        }

        /*
         * Use the application's existing
         * save method when available.
         */
        if (
            typeof window
                .persistScientificDraft ===
            'function'
        ) {
            try {
                window.persistScientificDraft(
                    scientificObject
                );

                return;
            } catch (error) {
                console.warn(
                    'The custom scientific draft save failed.',
                    error
                );
            }
        }

        try {
            window.localStorage.setItem(
                storageKey(),
                JSON.stringify(
                    scientificObject
                )
            );
        } catch (error) {
            console.warn(
                'Unable to persist the scientific draft.',
                error
            );
        }
    }

    function ensureTokens() {
        const scientificObject =
            getScientificObject();

        if (!scientificObject) {
            return [];
        }

        let changed = false;

        scientificObject.craft.forEach(
            function (craftItem) {
                if (
                    !craftItem ||
                    typeof craftItem !==
                        'object'
                ) {
                    return;
                }

                if (
                    typeof craftItem
                        .craftToken !==
                        'string' ||
                    !UUID_PATTERN.test(
                        craftItem.craftToken
                    )
                ) {
                    craftItem.craftToken =
                        createToken();

                    changed = true;
                }
            }
        );

        if (changed) {
            persistScientific();
        }

        return scientificObject.craft;
    }

    function normalizeToken(value) {
        return String(
            value || ''
        ).trim();
    }

    function findIndex(craftToken) {
        const token =
            normalizeToken(craftToken);

        if (!UUID_PATTERN.test(token)) {
            return -1;
        }

        return ensureTokens().findIndex(
            function (craftItem) {
                return (
                    craftItem &&
                    craftItem.craftToken ===
                        token
                );
            }
        );
    }

    function urlCraftToken() {
        const url =
            new URL(
                window.location.href
            );

        return normalizeToken(
            window
                .currentScientificCraftToken ||
            url.searchParams.get(
                'craftToken'
            ) ||
            ''
        );
    }

    function currentContext() {
        const craftToken =
            urlCraftToken();

        if (
            !UUID_PATTERN.test(
                craftToken
            )
        ) {
            throw new Error(
                'A valid temporary craft token is required.'
            );
        }

        const index =
            findIndex(craftToken);

        if (index < 0) {
            throw new Error(
                'The selected sampling craft was not found in the current draft.'
            );
        }

        const scientificObject =
            getScientificObject();

        return {
            token:
                craftToken,

            index:
                index,

            craft:
                scientificObject
                    .craft[index]
        };
    }

    function routeUrl(
        routeName,
        craftToken
    ) {
        const routes =
            window.scientificRoutes &&
            typeof window
                .scientificRoutes ===
                'object'
                ? window
                    .scientificRoutes
                : {};

        const route =
            routes[routeName] ||
            DEFAULT_ROUTES[routeName];

        if (!route) {
            throw new Error(
                'Scientific route configuration is missing: ' +
                routeName
            );
        }

        const url =
            new URL(
                route,
                window.location.origin
            );

        url.searchParams.set(
            'craftToken',
            craftToken
        );

        return (
            url.pathname +
            url.search +
            url.hash
        );
    }

    function boatGearClass(index) {
        try {
            if (
                typeof boatGearAvailable ===
                'function'
            ) {
                return String(
                    boatGearAvailable(
                        index
                    ) || ''
                );
            }
        } catch (error) {
            /*
             * Use the window fallback.
             */
        }

        if (
            typeof window
                .boatGearAvailable ===
            'function'
        ) {
            return String(
                window.boatGearAvailable(
                    index
                ) || ''
            );
        }

        return '';
    }

    function lengthClass(index) {
        try {
            if (
                typeof lengthAvailable ===
                'function'
            ) {
                return String(
                    lengthAvailable(
                        index
                    ) || ''
                );
            }
        } catch (error) {
            /*
             * Use the window fallback.
             */
        }

        if (
            typeof window.lengthAvailable ===
            'function'
        ) {
            return String(
                window.lengthAvailable(
                    index
                ) || ''
            );
        }

        return '';
    }

    function actionLink(
        columnClass,
        routeName,
        label,
        token,
        extraClass
    ) {
        const column =
            $('<div>', {
                class: columnClass
            });

        const link =
            $('<a>', {
                href:
                    routeUrl(
                        routeName,
                        token
                    ),

                class:
                    'btn btn-primary btn-block ' +
                    String(
                        extraClass || ''
                    ),

                text:
                    label
            });

        column.append(link);

        return column;
    }

    function removeByToken(
        craftToken
    ) {
        const index =
            findIndex(craftToken);

        if (index < 0) {
            return;
        }

        let removed = false;

        try {
            if (
                typeof removeSamplingCraft ===
                'function'
            ) {
                removeSamplingCraft(
                    index
                );

                removed = true;
            }
        } catch (error) {
            removed = false;
        }

        if (
            !removed &&
            typeof window
                .removeSamplingCraft ===
                'function'
        ) {
            window.removeSamplingCraft(
                index
            );

            removed = true;
        }

        if (!removed) {
            const scientificObject =
                getScientificObject();

            scientificObject.craft.splice(
                index,
                1
            );

            persistScientific();
        }

        renderSampling();
    }

    function renderSampling() {
        const table =
            $('.sampling-table');

        if (!table.length) {
            return;
        }

        table.empty();

        const crafts =
            ensureTokens();

        if (crafts.length === 0) {
            table.append(
                $('<p>', {
                    class:
                        'text-muted',

                    text:
                        'No sampling crafts have been added.'
                })
            );

            return;
        }

        crafts.forEach(
            function (
                craftItem,
                index
            ) {
                if (
                    !craftItem ||
                    !craftItem.craftToken
                ) {
                    return;
                }

                const token =
                    craftItem.craftToken;

                const row =
                    $('<div>', {
                        class:
                            'row mb-5'
                    });

                const boatColumn =
                    $('<div>', {
                        class:
                            'col-xl-3 col-lg-12 col-md-12'
                    });

                const boatText =
                    $('<p>');

                /*
                 * Use text nodes rather than
                 * raw HTML concatenation.
                 */
                boatText.append(
                    document.createTextNode(
                        'Boat number: '
                    )
                );

                boatText.append(
                    $('<strong>', {
                        text:
                            String(
                                craftItem
                                    .boatNumber ||
                                ''
                            )
                    })
                );

                boatColumn.append(
                    boatText
                );

                const actionsColumn =
                    $('<div>', {
                        class:
                            'col-xl-9 col-lg-12 col-md-12'
                    });

                const actionsRow =
                    $('<div>', {
                        class: 'row'
                    });

                const gearClass =
                    boatGearClass(
                        index
                    );

                actionsRow.append(
                    actionLink(
                        'col-lg-3 mb-1',
                        'addSampleCraft',
                        'Boat & Gear',
                        token,
                        ''
                    ),

                    actionLink(
                        'col-lg-4 mb-1',
                        'catchData',
                        'Catch',
                        token,
                        gearClass
                    ),

                    actionLink(
                        'col-lg-4 mb-1',
                        'addLengthWeight',
                        'Length & Weight',
                        token,
                        gearClass
                    ),

                    actionLink(
                        'col-lg-3 mb-1',
                        'addOperationCost',
                        'Economic',
                        token,
                        lengthClass(index)
                    )
                );

                const removeButton =
                    $('<button>', {
                        type:
                            'button',

                        class:
                            'btn btn-danger btn-block ' +
                            'remove-sampling-craft btn-sm',

                        'data-craft-token':
                            token,

                        'aria-label':
                            'Remove sampling craft'
                    });

                removeButton.append(
                    $('<i>', {
                        class:
                            'fas fa-trash-alt',

                        'aria-hidden':
                            'true'
                    })
                );

                removeButton.on(
                    'click',
                    function () {
                        removeByToken(
                            token
                        );
                    }
                );

                actionsRow.append(
                    $('<div>', {
                        class:
                            'col-lg-2 mb-1'
                    }).append(
                        removeButton
                    )
                );

                actionsColumn.append(
                    actionsRow
                );

                row.append(
                    boatColumn,
                    actionsColumn
                );

                table.append(row);
            }
        );
    }

    function normalizedName(value) {
        return String(
            value || ''
        )
            .replace(
                /[^a-z0-9]/gi,
                ''
            )
            .toLowerCase();
    }

    function findCraftValue(
        value,
        expectedNames,
        depth,
        visited
    ) {
        if (
            !value ||
            typeof value !== 'object' ||
            depth < 0 ||
            visited.has(value)
        ) {
            return undefined;
        }

        visited.add(value);

        const expected =
            expectedNames.map(
                normalizedName
            );

        /*
         * Search current object.
         */
        for (
            const key of
                Object.keys(value)
        ) {
            if (
                expected.includes(
                    normalizedName(key)
                )
            ) {
                const result =
                    value[key];

                if (
                    result !== undefined &&
                    result !== null &&
                    result !== ''
                ) {
                    return result;
                }
            }
        }

        /*
         * Search child objects.
         */
        for (
            const key of
                Object.keys(value)
        ) {
            const nested =
                value[key];

            if (
                nested &&
                typeof nested ===
                    'object'
            ) {
                const result =
                    findCraftValue(
                        nested,
                        expectedNames,
                        depth - 1,
                        visited
                    );

                if (
                    result !== undefined
                ) {
                    return result;
                }
            }
        }

        return undefined;
    }

    function craftValue(
        craft,
        names
    ) {
        return findCraftValue(
            craft,
            names,
            4,
            new WeakSet()
        );
    }

    function setSelectValueOrText(
        select,
        expectedValue
    ) {
        if (
            expectedValue === undefined ||
            expectedValue === null ||
            expectedValue === ''
        ) {
            return false;
        }

        const expected =
            String(expectedValue)
                .trim();

        let matchedValue = null;

        select
            .find('option')
            .each(
                function () {
                    const value =
                        String(
                            $(this).val()
                        );

                    const text =
                        String(
                            $(this).text()
                        ).trim();

                    if (
                        value === expected ||
                        text.toLowerCase() ===
                            expected
                                .toLowerCase()
                    ) {
                        matchedValue =
                            $(this).val();

                        return false;
                    }

                    return true;
                }
            );

        if (matchedValue === null) {
            return false;
        }

        select.val(
            matchedValue
        );

        return true;
    }

    function initializeDependentDropdowns(
        context
    ) {
        const fisheryType =
            $('#fishery_type');

        const subCategory =
            $('#sub_cat2');

        if (
            !fisheryType.length ||
            !subCategory.length ||
            !context ||
            !context.craft
        ) {
            return;
        }

        if (dropdownTimer !== null) {
            window.clearInterval(
                dropdownTimer
            );
        }

        /*
         * Find the saved value even when it
         * is stored inside a nested boat/gear
         * object.
         */
        const savedFishery =
            craftValue(
                context.craft,
                [
                    'fishery_type',
                    'fisheryType',
                    'fishey_type',
                    'fisheyType'
                ]
            );

        const savedSubCategory =
            craftValue(
                context.craft,
                [
                    'sub_cat2',
                    'sub_category',
                    'subCategory',
                    'subcategory'
                ]
            );

        let attempts = 0;

        let firstChangeSent =
            false;

        let secondChangeSent =
            false;

        /*
         * The old Scientific JavaScript loads
         * dropdown options asynchronously.
         */
        dropdownTimer =
            window.setInterval(
                function () {
                    attempts += 1;

                    if (
                        savedFishery !==
                        undefined
                    ) {
                        setSelectValueOrText(
                            fisheryType,
                            savedFishery
                        );
                    }

                    const selectedFishery =
                        String(
                            fisheryType.val() ||
                            ''
                        );

                    /*
                     * Trigger the application's
                     * existing AJAX change handler.
                     */
                    if (
                        selectedFishery &&
                        !firstChangeSent
                    ) {
                        firstChangeSent =
                            true;

                        fisheryType.trigger(
                            'change'
                        );
                    }

                    const optionCount =
                        subCategory
                            .find('option')
                            .length;

                    if (optionCount > 0) {
                        if (
                            savedSubCategory !==
                            undefined
                        ) {
                            setSelectValueOrText(
                                subCategory,
                                savedSubCategory
                            );
                        }

                        if (
                            optionCount > 1 ||
                            attempts >= 30
                        ) {
                            window.clearInterval(
                                dropdownTimer
                            );

                            dropdownTimer =
                                null;

                            subCategory.trigger(
                                'change'
                            );

                            return;
                        }
                    }

                    /*
                     * Retry after 1.5 seconds in
                     * case the old handler was
                     * registered late.
                     */
                    if (
                        attempts === 15 &&
                        selectedFishery &&
                        !secondChangeSent
                    ) {
                        secondChangeSent =
                            true;

                        fisheryType.trigger(
                            'change'
                        );
                    }

                    /*
                     * Stop after 10 seconds.
                     */
                    if (attempts >= 100) {
                        window.clearInterval(
                            dropdownTimer
                        );

                        dropdownTimer =
                            null;

                        console.warn(
                            'Scientific subcategory options were not loaded.'
                        );
                    }
                },
                100
            );
    }

    function publishCurrentContext() {
        const token =
            urlCraftToken();

        /*
         * Main Scientific create page does
         * not have a craft token in the URL.
         */
        if (!token) {
            return null;
        }

        const context =
            currentContext();

        window.currentScientificCraftToken =
            context.token;

        window.currentScientificCraftIndex =
            context.index;

        window.currentScientificCraft =
            context.craft;

        if (
            publishedContextToken !==
            context.token
        ) {
            publishedContextToken =
                context.token;

            document.dispatchEvent(
                new CustomEvent(
                    'scientific:craft-context-ready',
                    {
                        detail:
                            context
                    }
                )
            );
        }

        initializeDependentDropdowns(
            context
        );

        return context;
    }

    function install() {
        /*
         * Overrides the old function that
         * generates:
         *
         * addsamplecraft?craft=0
         */
        window.loadSampling =
            renderSampling;

        ensureTokens();

        if (
            $('.sampling-table').length
        ) {
            renderSampling();
        }

        try {
            publishCurrentContext();
        } catch (error) {
            console.error(
                'Unable to initialize the scientific craft context.',
                error
            );
        }
    }

    window.ScientificCraftToken = {
        create:
            createToken,

        ensure:
            ensureTokens,

        findIndex:
            findIndex,

        current:
            currentContext,

        context:
            publishCurrentContext,

        remove:
            removeByToken,

        render:
            renderSampling,

        persist:
            persistScientific,

        initializeDropdowns:
            initializeDependentDropdowns,

        install:
            install
    };

    /*
     * Apply immediately.
     */
    window.loadSampling =
        renderSampling;

    /*
     * Apply again after existing scripts
     * and ready handlers have executed.
     */
    $(function () {
        install();

        window.setTimeout(
            install,
            0
        );

        window.setTimeout(
            install,
            250
        );

        window.setTimeout(
            install,
            1000
        );
    });

    /*
     * Handle browser back/forward cache.
     */
    window.addEventListener(
        'pageshow',
        function () {
            window.setTimeout(
                install,
                0
            );
        }
    );
})(
    window,
    document,
    window.jQuery
);