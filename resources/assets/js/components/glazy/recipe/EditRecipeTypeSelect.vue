<template>
    <div class="row" id="recipe-type-select" v-if="isLoaded">
        <div class="col-md-4">
            <multiselect
                    :options="baseTypeOptions"
                    v-model="baseTypeId"
                    @input="updateBaseType"
                    placeholder="Select Type"
                    :allow-empty="false"
                    key="value"
                    label="text"
                    track-by="value"
            ></multiselect>
        </div>
        <div class="col-md-8">
            <multiselect
                    v-if="subTypeOptions"
                    :options="subTypeOptions"
                    v-model="typeId"
                    @input="updateSubType"
                    placeholder="Select Type"
                    :allow-empty="true"
                    key="value"
                    label="text"
                    track-by="value"
            ></multiselect>
        </div>
    </div>
</template>

<script>

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import Multiselect from 'vue-multiselect';

  export default {

    props: {
      typeId: {
        type: Number,
        default: 0
      },
      baseTypeId: {
        type: Number,
        default: 0
      }
    },

    components: {
      Multiselect
    },

    computed: {

      isLoaded: function () {
        if (this.value) {
          return true;
        }
        return false;
      },

      baseTypeOptions: function () {
        return MaterialTypes.getParentTypes();
      },

      subTypeOptions: function () {
        if (this.isLoaded) {
          switch (this.baseTypeId) {
            case MaterialTypes.GLAZE_TYPE_ID:
              return MaterialTypes.getGlazeTypes();
            case MaterialTypes.CLAYS_TYPE_ID:
              return MaterialTypes.getClayTypes();
            case MaterialTypes.SLIPS_TYPE_ID:
              return MaterialTypes.getSlipTypes();
          }
        }
        return null;
      }
    },

    methods: {

      updateBaseType () {
        // this.value.id.value = null;
        this.typeId = 0;

        /*
        this.$emit('input', material_data);

        console.log('TYPE: update base type: ' + value);
        this.value = {};
        this.value.base_id = Number(value);
        this.value.id = null;
        this.updateValue();
        */
      },

      updateSubType (value) {
        /*
        var tmpBaseId = this.value.base_id;
        this.value = {};
        this.value.base_id = tmpBaseId;
        this.value.id = Number(value);
        console.log('TYPE: update sub type: ' + value + ' actual var val:' + this.value.id);
        this.updateValue();
        */
      },

      updateValue () {

        // Check if a 0 value was entered for subtype..
        if (this.value.id == 0) {
          this.value.id = null;
        }

        var material_data = {id: this.value.id, base_id: this.value.base_id};
        if (this.value.id == null && this.value.base_id != null) {
          material_data.id = this.value.base_id;
        }
        console.log('TYPE Emit: id:' + material_data.id + 'base_id' + material_data.base_id);
        this.$emit('input', material_data);
      }

    }
  }
</script>

