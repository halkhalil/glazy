export default class SearchQuery {

  constructor () {

    this.params = {
      u: 0,
      collection: null,
      primitive: null,
      keywords: '',
      base_type: null,
      type: null,
      cone: null,
      atmosphere: null,
      surface: null,
      transparency: null,
      hex_color: '',
      p: 0,
      view: 'cards',
      order: '',
      oxide1: 'Al2O3',
      oxide2: 'SiO2',
      oxide3: 'Fe2O3',
      isThreeAxes: false
    }
  }

  setParams(params) {
    this.params = params
//    this.params = Object.assign({}, params)
    //this.params = params;
  }

  getMinimalQuery() {
    var minimalQuery = {};
    if (this.params.u) {
      minimalQuery.u = this.params.u
    }
    if (this.params.collection !== 0) {
      // DAU special case, -1 signifies user searching for own recipes
      minimalQuery.collection = this.params.collection
    }
    if (this.params.primitive) {
      minimalQuery.primitive = this.params.primitive
    }
    if (this.params.keywords) {
      minimalQuery.keywords = this.params.keywords
    }
    if (this.params.base_type) {
      minimalQuery.base_type = this.params.base_type
    }
    if (this.params.type) {
      minimalQuery.type = this.params.type
    }
    if (this.params.cone) {
      minimalQuery.cone = this.params.cone
    }
    if (this.params.atmosphere) {
      minimalQuery.atmosphere = this.params.atmosphere
    }
    if (this.params.surface) {
      minimalQuery.surface = this.params.surface
    }
    if (this.params.transparency) {
      minimalQuery.transparency = this.params.transparency
    }
    if (this.params.hex_color) {
      minimalQuery.hex_color = this.params.hex_color
    }
    if (this.params.p) {
      minimalQuery.p = this.params.p
    }
    if (this.params.view) {
      minimalQuery.view = this.params.view
    }
    if (this.params.order) {
      minimalQuery.order = this.params.order
    }
    if (this.params.oxide1) {
      minimalQuery.oxide1 = this.params.oxide1
    }
    if (this.params.oxide2) {
      minimalQuery.oxide2 = this.params.oxide2
    }
    if (this.params.isThreeAxes) {
      if (this.params.oxide3) {
        minimalQuery.oxide3 = this.params.oxide3
      }
    }

    return minimalQuery;
  }

  setFromRouterQuery(routerQuery) {
    console.log('IN SET FROM ROUTER QUERY')
    console.log(routerQuery)
    if (routerQuery) {
      if ('u' in routerQuery && routerQuery.u) {
        this.params.u = Number(routerQuery.u)
      }
      if ('collection' in routerQuery && routerQuery.collection) {
        this.params.collection = Number('' in collection && routerQuery.collection)
      }
      if ('primitive' in routerQuery && routerQuery.primitive) {
        this.params.primitive = Number(routerQuery.primitive)
      }
      if ('keywords' in routerQuery && routerQuery.keywords) {
        this.params.keywords = routerQuery.keywords
      }
      if ('base_type' in routerQuery && routerQuery.base_type) {
        this.params.base_type = Number(routerQuery.base_type)
      }
      if ('type' in routerQuery && routerQuery.type) {
        console.log('set from routerquery type: ' + routerQuery.type)
        this.params.type = Number(routerQuery.type)
      }
      if ('cone' in routerQuery && routerQuery.cone) {
        this.params.cone = routerQuery.cone
      }
      if ('atmosphere' in routerQuery && routerQuery.atmosphere) {
        this.params.atmosphere = Number(routerQuery.atmosphere)
      }
      if ('surface' in routerQuery && routerQuery.surface) {
        this.params.surface = Number(routerQuery.surface)
      }
      if ('transparency' in routerQuery && routerQuery.transparency) {
        this.params.transparency = Number(routerQuery.transparency)
      }
      if ('hex_color' in routerQuery && routerQuery.hex_color) {
        this.params.hex_color = routerQuery.hex_color
      }
      if ('p' in routerQuery && routerQuery.p) {
        this.params.p = routerQuery.p
      }
      if ('view' in routerQuery && routerQuery.view) {
        this.params.view = routerQuery.view
      }
      if ('order' in routerQuery && routerQuery.order) {
        this.params.order = routerQuery.order
      }
      if ('oxide1' in routerQuery && routerQuery.oxide1) {
        this.params.oxide1 = routerQuery.oxide1
      }
      if ('oxide2' in routerQuery && routerQuery.oxide2) {
        this.params.oxide2 = routerQuery.oxide2
      }
      if ('oxide3' in routerQuery && routerQuery.oxide3) {
        this.params.oxide3 = routerQuery.oxide3
      }
      if (this.params.oxide3) {
        this.params.isThreeAxes = true
      } else {
        this.params.oxide3 = 'Fe2O3'
        this.params.isThreeAxes = false
      }
    }
  }

  getFormParams() {
    return {
      keywords: this.params.keywords,
      collection: { value: this.params.collection },
      base_type: { value: this.params.base_type },
      type: { value: this.params.type },
      cone: { value: this.params.cone},
      atmosphere: { value: this.params.atmosphere},
      surface: { value: this.params.surface},
      transparency: { value: this.params.transparency},
      hex_color: this.params.hex_color,
      oxide1: { value: this.params.oxide1},
      oxide2: { value: this.params.oxide2},
      oxide3: { value: this.params.oxide3},
      isThreeAxes: this.params.isThreeAxes
    }
  }

  setFormParams(params) {
    this.params.keywords = params.keywords
    this.params.collection = params.collection.value
    this.params.base_type = params.base_type.value
    this.params.type = params.type.value
    this.params.cone = params.cone.value
    this.params.atmosphere = params.atmosphere.value
    this.params.surface = params.surface.value
    this.params.transparency = params.transparency.value
    this.params.hex_color = params.hex_color
    this.params.oxide1 = params.oxide1.value
    this.params.oxide2 = params.oxide2.value
    this.params.oxide3 = params.oxide3.value
    this.params.isThreeAxes = params.isThreeAxes
  }

  // https://stackoverflow.com/questions/1714786/query-string-encoding-of-a-javascript-object
  // console.log(serialize({foo: "hi there", bar: "100%" }));
  serialize (obj) {
    var str = [];
    for(var p in obj)
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");
  }

  toQuerystring(obj) {
    return this.serialize(obj)
  }

}

