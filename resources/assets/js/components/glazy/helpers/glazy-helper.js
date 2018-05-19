import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'

export default class GlazyHelper {

  getMaterialTypeString (material) {
    return GlazyHelper.MATERIAL_TYPES.LOOKUP[material.materialTypeId]
  }

  getR2ORORatioString (material) {
    if (material.analysis &&
      material.analysis.umfAnalysis &&
      material.analysis.umfAnalysis.R2OTotal &&
      material.analysis.umfAnalysis.ROTotal) {
      return (Number(material.analysis.umfAnalysis.R2OTotal).toFixed(1) + '').substr(1)
        + ' : ' +
        (Number(material.analysis.umfAnalysis.ROTotal).toFixed(1) + '').substr(1)
    }
    return ''
  }

  hasThumbnail (material) {
    if (material.hasOwnProperty('thumbnail')
      && material.thumbnail.hasOwnProperty('url')
      && material.thumbnail.url) {
      return true;
    }
    return false;
  }

  getImageUrl (material, image, size) {
    if (image && image.filename) {
      var id = '' + material.id;
      var bin = id.substr(id.length - 2);
      return GLAZY_APP_URL + '/storage/uploads/recipes/' +
        bin + size + image.filename;
    }
    return '/img/recipes/black.png';
  }

  getSmallImageUrl (material, image) {
    return this.getImageUrl(material, image, '/s_')
  }

  getPreImageUrl (material, image) {
    return this.getImageUrl(material, image, '/p_')
  }

  getConeString (material) {
    var coneString = '?'
    if (material.fromOrtonConeId
      && material.toOrtonConeId
      && material.fromOrtonConeId != material.toOrtonConeId) {
      return GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.fromOrtonConeId] +
        "-" + GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.toOrtonConeId]
    }

    if (material.fromOrtonConeId) {
      return coneString = GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.fromOrtonConeId]
    }

    if (material.toOrtonConeId) {
      coneString = GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.toOrtonConeId]
    }
    return coneString
  }

  getAtmospheresString (material) {
    var str = '';
    if (material.atmospheres) {
      material.atmospheres.forEach((atmosphere) => {
        if (str.length) {
        str += ', '
      }
      str += GlazyHelper.CONSTANTS.ATMOSPHERE_SHORT_LOOKUP[atmosphere.id]
    })
    }
    return str
  }

  getUserProfileUrl (user) {
    if (user.hasOwnProperty('profile') &&
      user.profile.hasOwnProperty('username') &&
      user.profile.username) {
      return '/u/' + user.profile.username;
    }
    return '/u/' + user.id;
  }

  getUserDisplayName (user) {
    if (user.hasOwnProperty('profile') &&
      user.profile.hasOwnProperty('username') &&
      user.profile.username) {
      return user.profile.username;
    }
    return user.name;
  }

  getUserAvatar (user) {
    if (user.hasOwnProperty('profile') &&
      user.hasOwnProperty('avatar') && 
      user.profile.avatar) {
      return user.profile.avatar;
    }
    return 'http://www.gravatar.com/avatar/?d=mm';
  }

}

GlazyHelper.MATERIAL_TYPES = new MaterialTypes()
GlazyHelper.CONSTANTS = new GlazyConstants()