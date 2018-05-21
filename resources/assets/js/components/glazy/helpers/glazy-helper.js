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
      && material.thumbnail.hasOwnProperty('filename')
      && material.thumbnail.filename) {
      return true;
    }
    return false;
  }

  getImageUrl (material, image, size) {
    if (image && 
      'filename' in image &&
      image.filename) {
      var id = '' + material.id;
      var bin = id.substr(id.length - 2);
      return GLAZY_APP_URL + '/storage/uploads/recipes/' +
        bin + '/' + size + '_' + image.filename;
    }
    return '/img/recipes/black.png';
  }

  getPreImageUrl (material, image) {
    return this.getImageUrl(material, image, 'p')
  }

  getSmallImageUrl (material, image) {
    return this.getImageUrl(material, image, 's')
  }

  getSmallThumbnailUrl (material) {
    if (this.hasThumbnail(material)) {
      return this.getImageUrl(material, material.thumbnail, 's')
    }
    return '/img/recipes/black.png';
  }

  getMediumImageUrl (material, image) {
    return this.getImageUrl(material, image, 'm')
  }

  getLargeImageUrl (material, image) {
    return this.getImageUrl(material, image, 'l')
  }

  getConeString (material) {
    if ('fromOrtonConeId' in material &&
      'toOrtonConeId' in material &&
      material.fromOrtonConeId &&
      material.toOrtonConeId &&
      material.fromOrtonConeId != material.toOrtonConeId) {
      return GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.fromOrtonConeId] +
        "-" + GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.toOrtonConeId]
    }
    else if ('fromOrtonConeId' in material && material.fromOrtonConeId) {
      return GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.fromOrtonConeId]
    }
    else if ('toOrtonConeId' in material && material.toOrtonConeId) {
      return GlazyHelper.CONSTANTS.ORTON_CONES_LOOKUP[material.toOrtonConeId]
    }
    return '?'
  }

  /*
            coneString: function(fromOrtonConeName, toOrtonConeName) {
                var coneString = '?';
                if (fromOrtonConeName
                    && toOrtonConeName
                    && fromOrtonConeName != toOrtonConeName) {
                    return fromOrtonConeName + "-" + toOrtonConeName;
                }

                if (fromOrtonConeName) {
                    return coneString = fromOrtonConeName;
                }

                if (toOrtonConeName) {
                    coneString = toOrtonConeName;
                }
                return coneString;
            }

  */

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

  getUserProfileUrlId (user) {
    if (user.hasOwnProperty('profile') &&
      user.profile.hasOwnProperty('username') &&
      user.profile.username) {
      return user.profile.username;
    }
    return user.id;
  }

  getUserProfileUrl (user) {
    return '/u/' + this.getUserProfileUrlId(user)
  }

  getUserDisplayName (user) {
    /*
    if (user &&
      user.hasOwnProperty('profile') &&
      user.profile.hasOwnProperty('username') &&
      user.profile.username) {
      return user.profile.username;
    }
    return user.name;
    */
    // Only return profile username if name not found 
    if (!user.name && 
      (user.hasOwnProperty('profile') && 
      user.profile.hasOwnProperty('username') &&
      user.profile.username)) {
        return user.profile.username;
    }
    return user.name;
  }

  hasUserAvatar (user) {
    if (user &&
      user.hasOwnProperty('profile') &&
      user.profile.hasOwnProperty('avatar') && 
      user.profile.avatar) {
      return true
    }
    return false
  }

  getUserAvatar (user) {
    if (this.hasUserAvatar(user)) {
      return user.profile.avatar;
    }
    return 'https://www.gravatar.com/avatar/?d=mm';
  }

  getLinkifiedText (content) {
    if (!content) {
      return
    }
    // https://stackoverflow.com/questions/37684/how-to-replace-plain-urls-with-links
    // http://, https://, ftp://
    var urlPattern = /\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/gim;
    // www. sans http:// or https://
    var pseudoUrlPattern = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    // Email addresses
    var emailAddressPattern = /[\w.]+@[a-zA-Z_-]+?(?:\.[a-zA-Z]{2,6})+/gim;
    return content
      .replace(urlPattern, '<a href="$&">$&</a>')
      .replace(pseudoUrlPattern, '$1<a href="http://$2">$2</a>')
      .replace(emailAddressPattern, '<a href="mailto:$&">$&</a>');
  }

  /**
   * FILE UPLOAD METHODS
   */

  hasImageFileExtension (file) {
    if (file && 'name' in file) {
      return this.hasExtension(file.name, ['.jpg', '.jpeg', '.gif', '.png'])
    }
    return false
  }

  hasExtension (filename, exts) {
    // http://stackoverflow.com/questions/4234589/validation-of-file-extension-before-uploading-file
    return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$', "i")).test(filename);
  }

  isUnderMaxFileSize (file) {
    if (file && 'size' in file) {
      return this.checkFileSize(file.size)
    }
    return false
  }

  checkFileSize (filesize) {
    if ((filesize / 1048576) > GlazyHelper.MAX_UPLOAD_SIZE_MB) {
      return false;
    }
    return true;
  }

}

GlazyHelper.MATERIAL_TYPES = new MaterialTypes()
GlazyHelper.CONSTANTS = new GlazyConstants()
GlazyHelper.MAX_UPLOAD_SIZE_MB = 6
GlazyHelper.AVATAR_URL = 'https://www.gravatar.com/avatar/?d=mm'
