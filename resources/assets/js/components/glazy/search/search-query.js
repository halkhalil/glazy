export default class SearchQuery {

  constructor ( params ) {
    this.defaultX = 'SiO2'
    this.defaultY = 'Al2O3'
    this.defaultView = 'cards'
    this.userSelfSearchString = 'user'

    this.params = {
      u: 0,
      collection: 0,
      keywords: '',
      username: '',
      base_type: 0,
      type: 0,
      cone: 0,
      atmosphere: 0,
      surface: 0,
      transparency: 0,
      country: 0,
      hex_color: '',
      color: {},
      p: 0,
      view: this.defaultView,
      order: '',
      x: this.defaultX,
      y: this.defaultY,
      images: 0 // is this an image search? 1 == true
    }

    if (params) {
      this.setFromRouterQuery(params)
    }
  }

  setParams(params) {
    this.params = params
  }
  /*
  copyParams(params) {
    this.params = Object.assign({}, params)
  }
  */
  getMinimalQuery() {
    var minimalQuery = {};
    if (this.params.u) {
      minimalQuery.u = this.params.u
    }
    if (this.params.collection) {
      minimalQuery.collection = this.params.collection
    }
    if (this.params.keywords) {
      minimalQuery.keywords = this.params.keywords
    }
    if (this.params.username) {
      minimalQuery.username = this.params.username
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
    if (this.params.country) {
      minimalQuery.country = this.params.country
    }
    if (this.params.hex_color) {
      minimalQuery.hex_color = this.params.hex_color
    }
    if (this.params.p) {
      minimalQuery.p = this.params.p
    }
    if (this.params.view && this.params.view !== this.defaultView) {
      minimalQuery.view = this.params.view
    }
    if (this.params.order) {
      minimalQuery.order = this.params.order
    }
    if (this.params.y && this.params.x &&
      this.params.y !== this.defaultY && this.params.x !== this.defaultX) {
      minimalQuery.y = this.params.y
      minimalQuery.x = this.params.x
    }
    if (this.params.images) {
      minimalQuery.images = this.params.images
    }
    return minimalQuery;
  }

  setFromRouterQuery(routerQuery) {
    if (routerQuery) {
      if ('u' in routerQuery && routerQuery.u) {
        this.params.u = Number(routerQuery.u)
      }
      if ('collection' in routerQuery && routerQuery.collection) {
        this.params.collection = Number(routerQuery.collection)
      }
      if ('keywords' in routerQuery && routerQuery.keywords) {
        this.params.keywords = routerQuery.keywords
      }
      if ('username' in routerQuery && routerQuery.username) {
        this.params.username = routerQuery.username
      }
      if ('base_type' in routerQuery && routerQuery.base_type) {
        this.params.base_type = Number(routerQuery.base_type)
      }
      if ('type' in routerQuery && routerQuery.type) {
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
      if ('country' in routerQuery && routerQuery.country) {
        this.params.country = Number(routerQuery.country)
      }
      if ('hex_color' in routerQuery && routerQuery.hex_color) {
        this.params.hex_color = routerQuery.hex_color
        this.params.color = { hex: '#' + routerQuery.hex_color }
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
      if ('oxide1' in routerQuery && routerQuery.y) {
        this.params.y = routerQuery.y
      }
      if ('x' in routerQuery && routerQuery.x) {
        this.params.x = routerQuery.x
      }
      if ('images' in routerQuery && routerQuery.images) {
        this.params.images = routerQuery.images
      }
    }
  }

  setFromSearchForm(params) {
    if (params) {
      this.params.u = Number(params.u)
      this.params.collection = Number(params.collection)
      this.params.keywords = params.keywords
      this.params.username = params.username
      this.params.base_type = Number(params.base_type)
      this.params.type = Number(params.type)
      this.params.cone = params.cone
      this.params.atmosphere = Number(params.atmosphere)
      this.params.surface = Number(params.surface)
      this.params.transparency = Number(params.transparency)
      this.params.country = Number(params.country)
      this.params.hex_color = params.hex_color
      this.params.y = params.y
      this.params.x = params.x
      this.params.images = params.images
    }
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

