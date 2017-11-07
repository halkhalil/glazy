export default class SearchQuery {

    /**
     * TODO: REFACTOR, replace with generic querystring functions
     */
    constructor () {
        this.search_params = {
            user_id: 0,
            collection_id : null,
            is_primitive : null,
            keywords : '',
            base_type_id: null,
            type_id: null,
            cone_id: null,
            atmosphere_id: null,
            surface_type_id: null,
            transparency_type_id: null,
            hex_color: '',
            page_num: 0,
            view : '',
            order : '',
            oxide1: 'Al2O3',
            oxide2: 'SiO2',
            oxide3: 'Fe2O3',
            isThreeAxes: false
        }
    }

    getClone() {
      var cloned = new SearchQuery();
      cloned.setParam('user_id', this.getParam('user_id'));
      cloned.setParam('collection_id', this.getParam('collection_id'));
      cloned.setParam('is_primitive', this.getParam('is_primitive'));
      cloned.setParam('keywords', this.getParam('keywords'));
      cloned.setParam('base_type_id', this.getParam('base_type_id'));
      cloned.setParam('type_id', this.getParam('type_id'));
      cloned.setParam('cone_id', this.getParam('cone_id'));
      cloned.setParam('atmosphere_id', this.getParam('atmosphere_id'));
      cloned.setParam('surface_type_id', this.getParam('surface_type_id'));
      cloned.setParam('transparency_type_id', this.getParam('transparency_type_id'));
      cloned.setParam('hex_color', this.getParam('hex_color'));
      cloned.setParam('page_num', this.getParam('page_num'));
      cloned.setParam('view', this.getParam('view'));
      cloned.setParam('order', this.getParam('order'));
      cloned.setParam('oxide1', this.getParam('oxide1'));
      cloned.setParam('oxide2', this.getParam('oxide2'));
      cloned.setParam('oxide3', this.getParam('oxide3'));
      cloned.setParam('isThreeAxes', this.getParam('isThreeAxes'));
      return cloned;
    }

    getMinimalQuery () {
      var minimalQuery = {};
      if (this.getParam('user_id')) {
        minimalQuery.u = this.getParam('user_id')
      }
      if (this.getParam('collection_id')) {
        minimalQuery.collection = this.getParam('collection_id')
      }
      if (this.getParam('is_primitive')) {
        minimalQuery.primitive = this.getParam('is_primitive')
      }
      if (this.getParam('keywords')) {
        minimalQuery.keywords = this.getParam('keywords')
      }
      if (this.getParam('base_type_id')) {
        minimalQuery.base_type = this.getParam('base_type_id')
      }
      if (this.getParam('type_id')) {
        minimalQuery.type = this.getParam('type_id')
      }
      if (this.getParam('cone_id')) {
        minimalQuery.cone = this.getParam('cone_id')
      }
      if (this.getParam('atmosphere_id')) {
        minimalQuery.atmosphere = this.getParam('atmosphere_id')
      }
      if (this.getParam('surface_type_id')) {
        minimalQuery.surface = this.getParam('surface_type_id')
      }
      if (this.getParam('transparency_type_id')) {
        minimalQuery.transparency = this.getParam('transparency_type_id')
      }
      if (this.getParam('hex_color')) {
        minimalQuery.hex_color = this.getParam('hex_color')
      }
      if (this.getParam('page_num')) {
        minimalQuery.p = this.getParam('page_num')
      }
      if (this.getParam('view')) {
        minimalQuery.view = this.getParam('view')
      }
      if (this.getParam('order')) {
        minimalQuery.order = this.getParam('order')
      }
      if (this.getParam('oxide1')) {
        minimalQuery.order = this.getParam('oxide1')
      }
      if (this.getParam('oxide2')) {
        minimalQuery.order = this.getParam('oxide2')
      }
      if (this.getParam('isThreeAxes')) {
        if (this.getParam('oxide3')) {
          minimalQuery.order = this.getParam('oxide3')
        }
      }

      return minimalQuery;
    }

    /**
     * https://gomakethings.com/how-to-get-the-value-of-a-querystring-with-native-javascript/
     * Get the value of a querystring
     * @param  {String} field The field to get the value of
     * @param  {String} url   The URL to get the value from (optional)
     * @return {String}       The field value
     */
    getQueryString( field, url, default_value ) {
        var href = url ? url : window.location.href;
        var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
        var string = reg.exec(href);
        return string ? string[1] : default_value;
    }

    setParam(name, value) {
        if(!this.search_params.hasOwnProperty(name)) {
            console.log('Error in setting search param: name not found: ' + name);
            return;
        }

        this.search_params[name] = value;
    }

    setParams(params) {
        this.search_params = params;
    }

    getParam(name) {
        return this.search_params[name];
    }

    setFromUrl(url = null) {
      this.search_params.user_id = Number(this.getQueryString('u', url, 0))
      this.search_params.collection_id = Number(this.getQueryString('collection', url, 0))
      this.search_params.is_primitive = Number(this.getQueryString('primitive', url, 0))
      this.search_params.keywords = this.getQueryString('keywords', url, '')
      this.search_params.base_type_id = Number(this.getQueryString('base_type', url, 0))
      this.search_params.type_id = Number(this.getQueryString('type', url, 0))
      this.search_params.cone_id = this.getQueryString('cone', url, '')
      this.search_params.atmosphere_id = Number(this.getQueryString('atmosphere', url, 0))
      this.search_params.surface_type_id = Number(this.getQueryString('surface', url, 0))
      this.search_params.transparency_type_id = Number(this.getQueryString('transparency', url, 0))
      this.search_params.hex_color = this.getQueryString('hex_color', url, '')
      this.search_params.page_num = this.getQueryString('p', url, 0)
      this.search_params.view = this.getQueryString('view', url, '')
      this.search_params.order = this.getQueryString('order', url, '')
      this.search_params.oxide1 = this.getQueryString('oxide1', url, null)
      this.search_params.oxide2 = this.getQueryString('oxide2', url, null)
      this.search_params.oxide3 = this.getQueryString('oxide3', url, null)
      if (this.search_params.oxide3) {
        this.search_params.isThreeAxes = true
      } else {
        this.search_params.oxide3 = 'Fe2O3'
        this.search_params.isThreeAxes = false
      }
    }

  setFromRouterQuery(query) {
        if (query) {
          if ('u' in query && query.u) {
            this.search_params.user_id = Number(query.u)
          }
          if ('collection' in query && query.collection) {
            this.search_params.collection_id = Number('' in collection && query.collection)
          }
          if ('primitive' in query && query.primitive) {
            this.search_params.is_primitive = Number(query.primitive)
          }
          if ('keywords' in query && query.keywords) {
            this.search_params.keywords = query.keywords
          }
          if ('base_type' in query && query.base_type) {
            this.search_params.base_type_id = Number(query.base_type)
          }
          if ('type' in query && query.type) {
            this.search_params.type_id = Number(query.type)
          }
          if ('cone' in query && query.cone) {
            this.search_params.cone_id = query.cone
          }
          if ('atmosphere' in query && query.atmosphere) {
            this.search_params.atmosphere_id = Number(query.atmosphere)
          }
          if ('surface' in query && query.surface) {
            this.search_params.surface_type_id = Number(query.surface)
          }
          if ('transparency' in query && query.transparency) {
            this.search_params.transparency_type_id = Number(query.transparency)
          }
          if ('hex_color' in query && query.hex_color) {
            this.search_params.hex_color = query.hex_color
          }
          if ('p' in query && query.p) {
            this.search_params.page_num = query.p
          }
          if ('view' in query && query.view) {
            this.search_params.view = query.view
          }
          if ('order' in query && query.order) {
            this.search_params.order = query.order
          }
          if ('oxide1' in query && query.oxide1) {
            this.search_params.oxide1 = query.oxide1
          }
          if ('oxide2' in query && query.oxide2) {
            this.search_params.oxide2 = query.oxide2
          }
          if ('oxide3' in query && query.oxide3) {
            this.search_params.oxide3 = query.oxide3
          }
          if (this.search_params.oxide3) {
            this.search_params.isThreeAxes = true
          } else {
            this.search_params.oxide3 = 'Fe2O3'
            this.search_params.isThreeAxes = false
          }
        }
  }


  toQuerystring(output_ignore = null) {

        if (!output_ignore) { output_ignore = {}; }

        var qs = '';
        var hasParam = false;

        if (this.search_params['user_id']
            && this.search_params['user_id'] > 0
            && !output_ignore.hasOwnProperty('user_id')) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'u=' + this.search_params['user_id'];
            hasParam = true;
        }

        if (this.search_params['collection_id']
            && this.search_params['collection_id'] > 0
            && !output_ignore.hasOwnProperty('collection_id')) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'collection=' + this.search_params['collection_id'];
            hasParam = true;
        }

        if (this.search_params['is_primitive']
            && this.search_params['is_primitive'] > 0
            && !output_ignore.hasOwnProperty('is_primitive')) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'primitive=' + this.search_params['is_primitive'];
            hasParam = true;
        }

        if (this.search_params['keywords']) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'keywords=' + this.search_params['keywords'];
            hasParam = true;
        }

        if (this.search_params['base_type_id']
            && this.search_params['base_type_id'] > 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'base_type=' + this.search_params['base_type_id'];
            hasParam = true;
        }

        if (this.search_params['type_id']
            && this.search_params['type_id'] > 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'type=' + this.search_params['type_id'];
            hasParam = true;
        }

        if (this.search_params['cone_id']
            && this.search_params['cone_id'] != 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'cone=' + this.search_params['cone_id'];
            hasParam = true;
        }

        if (this.search_params['atmosphere_id']
            && this.search_params['atmosphere_id'] != 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'atmosphere=' + this.search_params['atmosphere_id'];
            hasParam = true;
        }

        if (this.search_params['surface_type_id']
            && this.search_params['surface_type_id'] != 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'surface=' + this.search_params['surface_type_id'];
            hasParam = true;
        }

        if (this.search_params['transparency_type_id']
            && this.search_params['transparency_type_id'] != 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'transparency=' + this.search_params['transparency_type_id'];
            hasParam = true;
        }

        if (this.search_params['hex_color']) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'hex_color=' + this.search_params['hex_color'];
            hasParam = true;
        }

        if (this.search_params['page_num']
            && this.search_params['page_num'] != 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'p=' + this.search_params['page_num'];
            hasParam = true;
        }

        if (this.search_params['view']
            && this.search_params['view'] != 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'view=' + this.search_params['view'];
            hasParam = true;
        }

        if (this.search_params['order']
            && this.search_params['order'] != 0) {
            if (hasParam) {
                qs += '&';
            }
            qs += 'order=' + this.search_params['order'];
            hasParam = true;
        }

    if (this.search_params['oxide1']
      && this.search_params['oxide1'] > 0) {
      if (hasParam) {
        qs += '&';
      }
      qs += 'u=' + this.search_params['oxide1'];
      hasParam = true;
    }
    if (this.search_params['oxide2']
      && this.search_params['oxide2'] > 0) {
      if (hasParam) {
        qs += '&';
      }
      qs += 'u=' + this.search_params['oxide2'];
      hasParam = true;
    }
    if (this.search_params.isThreeAxes) {
      if (this.search_params['oxide3']
        && this.search_params['oxide3'] > 0) {
        if (hasParam) {
          qs += '&';
        }
        qs += 'u=' + this.search_params['oxide3'];
        hasParam = true;
      }
    }


    /*
            if (this.search_params['']
                && this.search_params[''] > 0) {
                if (hasParam) {
                    qs += '&';
                }
                qs += 'u=' + this.search_params[''];
                 hasParam = true;
            }
    */
        return qs;
    }



}

