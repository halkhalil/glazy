import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'

export default class MaterialHelper {

  constructor ( material ) {
    this.material = material
    this.materialTypes = new MaterialTypes()
    this.constants = new GlazyConstants()
  }

  getMaterialTypeString () {
    return this.materialTypes.LOOKUP[this.material.materialTypeId]
  }

  getR2ORORatioString () {
    if (this.material.analysis &&
      this.material.analysis.umfAnalysis &&
      this.material.analysis.umfAnalysis.R2OTotal &&
      this.material.analysis.umfAnalysis.ROTotal) {
      return (Number(this.material.analysis.umfAnalysis.R2OTotal).toFixed(1) + '').substr(1)
        + ' : ' +
        (Number(this.material.analysis.umfAnalysis.ROTotal).toFixed(1) + '').substr(1)
    }
    return ''
  }

  hasThumbnail () {
    if (recipe.hasOwnProperty('thumbnail')
      && recipe.thumbnail.hasOwnProperty('url')
      && recipe.thumbnail.url) {
      return true;
    }
    return false;
  }

  getImageUrl (image) {
    if (image && image.filename) {
      var id = '' + this.material.id;
      var bin = id.substr(id.length - 2);
      return GLAZY_APP_URL + '/storage/uploads/recipes/' +
        bin + '/s_' + image.filename;
    }
    return '/img/recipes/black.png';
  }

  getConeString () {
    var coneString = '?'
    if (this.material.fromOrtonConeId
      && this.material.toOrtonConeId
      && this.material.fromOrtonConeId != this.material.toOrtonConeId) {
      return this.constants.ORTON_CONES_LOOKUP[this.material.fromOrtonConeId] +
        "-" + this.constants.ORTON_CONES_LOOKUP[this.material.toOrtonConeId]
    }

    if (this.material.fromOrtonConeId) {
      return coneString = this.constants.ORTON_CONES_LOOKUP[this.material.fromOrtonConeId]
    }

    if (this.material.toOrtonConeId) {
      coneString = this.constants.ORTON_CONES_LOOKUP[this.material.toOrtonConeId]
    }
    return coneString
  }

  getAtmospheresString () {
    var str = '';
    if (this.material.atmospheres) {
      this.material.atmospheres.forEach((atmosphere) => {
        if (str.length) {
        str += ', '
      }
      str += this.constants.ATMOSPHERE_SHORT_LOOKUP[atmosphere.id]
    })
    }
    return str
  }

}

