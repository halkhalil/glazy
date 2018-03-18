<template>
    <div class="card firing-card card-plain">
        <div class="card-body text-center">
            <p class="firing-type">
                Orton Cone
                <a href="https://wiki.glazy.org/t/orton-cones/" target="_blank" class="help-link"><i class="fa fa-question-circle fa-fw"></i></a>
            </p>
            <h2 class="card-title" v-html="'&#9651;' + coneString"></h2>
            <p class="firing-type">
                Atmosphere
                <a href="https://wiki.glazy.org/t/firing-atmospheres/" target="_blank" class="help-link"><i class="fa fa-question-circle fa-fw"></i></a>
            </p>
            <p class="firing-atmospheres" v-html="atmospheres">
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
        background-color: #aaa;
        min-width: 8em;
    }

    .firing-card .card-body {
        padding: 10px;
    }

    .firing-card .card-body .card-title {
        color: #ffffff;
        font-size: 38px;
        margin-top: 0;
        margin-bottom: 0;
    }

    .firing-card .card-body .firing-type {
        font-size: 0.8rem;
        color: #555;
        margin-bottom: 0px;
    }
    .firing-card .card-body .firing-atmospheres {
        font-size: 1rem;
        color: #333333;
        margin-bottom: 0px;
    }


</style>
