<template>

    <div id="searchnavbar">


        <form method="GET" action="https://glazy.org/search"
              accept-charset="UTF-8" class="form-inline" role="search">

                <label class="sr-only" for="keywords">Keyword/ID</label>
                <input type="text" id="keywords" v-model="query.keywords" class="form-control" placeholder="Keyword/ID" value="">

                <select class="form-control"
                        v-model.number="query.base_type_id">
                    <option value="0">Select Type</option>
                    <option v-for="option in base_type_options" v-bind:value="option.value">
                        {{ option.text }}
                    </option>
                </select>

                <select class="form-control" v-if="subtype_options"
                        v-model.number="query.subtype_id">
                    <option value="0">Select Subtype</option>
                    <option v-for="option in subtype_options" v-bind:value="option.value">
                        {{ option.text }}
                    </option>
                </select>

                <select class="form-control" v-model="query.cone_id">
                    <option value="0">Temperature</option>
                    <option value="high">High-fire</option>
                    <option value="mid">Mid-fire</option>
                    <option value="low">Low-fire</option>
                    <option value="38">Δ14</option>
                    <option value="37">Δ13</option>
                    <option value="36">Δ12</option>
                    <option value="35">Δ11</option>
                    <option value="34">Δ10</option>
                    <option value="33">Δ9</option>
                    <option value="32">Δ8</option>
                    <option value="31">Δ7</option>
                    <option value="30">Δ6</option>
                    <option value="29">Δ5½</option>
                    <option value="28">Δ5</option>
                    <option value="27">Δ4</option>
                    <option value="26">Δ3</option>
                    <option value="25">Δ2</option>
                    <option value="24">Δ1</option>
                    <option value="23">Δ01</option>
                    <option value="22">Δ02</option>
                    <option value="21">Δ03</option>
                    <option value="20">Δ04</option>
                    <option value="19">Δ05</option>
                    <option value="18">Δ05½</option>
                    <option value="17">Δ06</option>
                    <option value="16">Δ07</option>
                    <option value="15">Δ08</option>
                    <option value="14">Δ09</option>
                    <option value="13">Δ010</option>
                    <option value="12">Δ011</option>
                    <option value="11">Δ012</option>
                    <option value="10">Δ013</option>
                    <option value="9">Δ014</option>
                    <option value="8">Δ015</option>
                    <option value="7">Δ016</option>
                    <option value="6">Δ017</option>
                    <option value="5">Δ018</option>
                    <option value="4">Δ019</option>
                    <option value="3">Δ020</option>
                    <option value="2">Δ021</option>
                    <option value="1">Δ022</option>
                </select>

                <select class="form-control" v-model.number="query.atmosphere_id">
                    <option value="0">Atmosphere</option>
                    <option value="1">Oxidation</option>
                    <option value="2">Neutral</option>
                    <option value="3">Reduction</option>
                    <option value="4">Salt &amp; Soda</option>
                    <option value="5">Wood</option>
                    <option value="6">Raku</option>
                    <option value="7">Luster</option>
                </select>

                <select class="form-control" v-model.number="query.surface_type_id">
                    <option value="0">Surface</option>
                    <option value="1">Matte</option>
                    <option value="2">Stony Matte</option>
                    <option value="3">Dry Matte</option>
                    <option value="4">Semi-matte</option>
                    <option value="5">Smooth Matte</option>
                    <option value="7">Satin-matte</option>
                    <option value="6">Satin</option>
                    <option value="9">Semi-glossy</option>
                    <option value="8">Glossy</option>
                </select>

                <select class="form-control" v-model.number="query.transparency_type_id">
                    <option value="0">Transparency</option>
                    <option value="1">Opaque</option>
                    <option value="2">Semi-opaque</option>
                    <option value="3">Translucent</option>
                    <option value="4">Transparent</option>
                </select>

                <a role="button" class="btn btn-block btn-default btn-outline text-center" id="cp-button">
                    <i class="fa fa-eyedropper color-eyedropper"></i> Color
                </a>
                <input type="hidden" v-model="query.hex_color" id="hex_color" class="color cp-input" value="" size="7">

                <button type="submit" class="btn btn-block btn-sm btn-info"
                        @click.prevent="search">
                    <i class="fa fa-search search-button"></i>
                    Search
                </button>

                <button type="submit" class="btn btn-block btn-sm btn-outline-info"
                        @click.prevent="resetSearch">
                    <i class="fa fa-refresh reset-button"></i>
                    Reset Search
                </button>

        </form>
    </div>

</template>


<script>

import SearchQuery from './search-query';
import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
//import MaterialTypes from '../../../ceramicscalc/material/material-types';

export default {
    name: 'SearchForm',

    props: [
        'query'
    ],

    data() {
        return {
            materialTypes: new MaterialTypes(),
            previousBaseTypeId: 0
        }
    },

    computed: {

        base_type_options: function() {
            return this.materialTypes.getParentTypes();
        },

        calc_base_type_id: function() {
            return this.query.base_type_id;
        },

        subtype_options: function() {
            if (this.calc_base_type_id && this.calc_base_type_id != 0) {
                if (this.previousBaseTypeId != 0) {
                    // we're switching base types.. set subtype to zero
                    this.query.subtype_id = 0;
                }
                this.previousBaseTypeId = this.calc_base_type_id;
                switch(this.calc_base_type_id) {
                    case this.materialTypes.GLAZE_TYPE_ID:
                        return this.materialTypes.getGlazeTypes();
                    case this.materialTypes.CLAYS_TYPE_ID:
                        return this.materialTypes.getClayTypes();
                    case this.materialTypes.SLIPS_TYPE_ID:
                        return this.materialTypes.getSlipTypes();
                }
            }
            return null;
        }

    },

    mounted() {
    },

    methods : {
        search: function() {
            this.$emit('searchrequest', this.query);
        },

//        resetFields: function()
        resetSearch: function() {
//            var emptyQuery = new SearchQuery();
            this.query.keywords = '';
            this.query.user_id = 0;
            this.query.base_type_id = 0;
            this.query.subtype_id = 0;
            this.query.cone_id = 0;
            this.query.atmosphere_id = 0;
            this.query.surface_type_id = 0;
            this.query.transparency_type_id = 0;
            this.query.hex_color = '';


//            this.query = emptyQuery.search_params;
            this.$emit('searchrequest', this.query);
        }
    }

}

</script>
