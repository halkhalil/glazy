<template>
    <div class="card firing-card card-plain">
        <div class="card-body text-center">
            <h2 class="card-title" v-html="'&#9651;' + coneString"></h2>
            <p class="firing-cone-type">Orton Cone</p>
            <p class="card-description">
                {{ atmospheres }}
            </p>
        </div>
    </div>
</template>
<script>

  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {

    name: 'FiringCard',

    props: {
      recipe: {
        type: Object,
        default: null
      }
    },

    data() {
      return {
        constants: new GlazyConstants()
      }
    },


    computed: {

            coneString: function() {
              // TODO: Move to util class
                var coneString = '?';
                if (this.recipe.fromOrtonConeName
                    && this.recipe.toOrtonConeName
                    && this.recipe.fromOrtonConeName != this.recipe.toOrtonConeName) {
                    return this.recipe.fromOrtonConeName + "-" + this.recipe.toOrtonConeName;
                }

                if (this.recipe.fromOrtonConeName) {
                    return coneString = this.recipe.fromOrtonConeName;
                }

                if (this.recipe.toOrtonConeName) {
                    coneString = this.recipe.toOrtonConeName;
                }
                return coneString;
            },

            atmospheres: function() {
                var atmospheres = '';
                var hasAtmosphere = false;
                if (this.recipe.atmospheres) {
                    for (var index in this.recipe.atmospheres) {
                        var atmosphere = this.recipe.atmospheres[index];
                        if (hasAtmosphere) {
                            atmospheres += ', ';
                        }
                        atmospheres += this.constants.ATMOSPHERE_LOOKUP[atmosphere.id];
                        hasAtmosphere = true;
                    }
                }
                return atmospheres;
            }
        }

    }
</script>

<style>

    .firing-card {
        background-color: #999999;
        min-width: 8em;
    }

    .firing-card .card-body {
        padding: 10px;
    }

    .firing-card .card-body .card-title {
        color: #ffffff;
        margin-top: 0;
        margin-bottom: 0;
    }

    .firing-card .card-body .firing-cone-type {
        font-size: .75rem;
        color: #666666;
        margin-bottom: 5px;
    }
    .firing-card .card-body .card-description {
        font-size: 1rem;
        color: #333333;
        margin-bottom: 5px;
    }


</style>
