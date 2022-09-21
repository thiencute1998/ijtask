<!--============== How to use =======================================
    when add schema to file main.js then Clear client db
    <div class="col-4 m-auto">
        <ijcore-select2-server2
          v-model="model"
          :url="$store.state.appRootApi + '/listing/api/common/list2'"
          table="employee"
          field-name="EmployeeName"
          field-i-d="EmployeeID"
          field-no="EmployeeNo"
          :field-type="1"
          :get-all="true"
          :client-d-b="true"
          placeholder="Chọn nhân viên"
          :allowClear="true"
          :settings="{maximumSelectionSize: 1}"
          :multiple="true">
        </ijcore-select2-server2>
      </div>

==================================================================-->
<template>
  <select class="form-control ijcore-select2-server2"></select>
</template>

<style lang="css">
.select2-table th, .select2-table td {
  font-weight: normal;
}
.ijcore-select2-server2 .select2-results__option {
  padding: 2px 6px;
}
.ijcore-select2-server2.select2-selection--multiple .select2-selection__choice {
  display: none;
}
.ijcore-select2-server2 .select2-search, .ijcore-select2-server2 .select2-search__field {
  width: 100% !important;
}
.ijcore-select2-server2.select2-selection {
  display: block;
  height: calc(1.5em + 0.55rem + 2px);
  padding: 0.275rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 400;
  line-height: 1.5;
  color: #5c6873;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #e4e7ea;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
}
.ijcore-select2-server2.select2-selection--multiple .select2-search__field {
  height: auto !important;
  padding: 0 !important;
}
.ijcore-select2-server2 .select2-selection__rendered {
  padding: 0 !important;
}
.select2-container--default.select2-container--focus .ijcore-select2-server2.select2-selection--multiple {
  color: #5c6873;
  background-color: #fff;
  outline: 0;
  border: 1px solid #aaa;
}
.select2-selection--single.ijcore-select2-server2 .select2-selection__rendered {
  height: 100%;
  padding: 0 !important;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.ijcore-select2-server2 .select2-selection__clear {
  order: 2;
  margin-right: 24px !important;
  top: -2px !important;
}
.select2-selection--single.ijcore-select2-server2 .select2-selection__clear {
  margin-right: 6px !important;
  order: 2;
}
.b-table .select2-selection, .b-table .select2-selection:focus, .b-table .select2-selection:active{
  border: none !important;
}
.b-table .ijcore-select2-server2.select2-selection {
  padding: 0.275rem 0.75rem 0.275rem 0.275rem;
}
.select2-container.ijcore-select2-container-server2 {
  width: 100% !important;
}
.ijcore-select2-server2 .select2-selection__clear {
  display: flex;
  align-items: center;
  justify-content: center;
}
.select2-selection--multiple:before {
  content: "";
  position: absolute;
  right: 7px;
  top: 42%;
  border-top: 5px solid #888;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  cursor: pointer;
}
</style>
<script>
  import ApiService from '@/services/api.service';
  import {TokenService} from "@/services/storage.service";
  import 'v-select2-component'
  import Dexie from "dexie"
  import qs from "qs";
  export default {
    props: {
      value: [String, Array, Object, Number],
      table: [String],
      FieldNo: [String, Number],
      FieldName: [String],
      FieldID: [String, Number],
      FieldUpdate: [Object, String, Array],
      FieldUpdateSpecial: [Object, String, Array],
      OnlyFieldUpdate: {
        type: Boolean,
        default: false
      },
      CoFieldNo: [String, Number],
      CoFieldName: [String],
      CoFieldID: [String, Number],
      FieldWhere: [Object, String, Array],
      FieldSelect: [Object, String, Array],
      extraParams: [String, Array, Object, Number],
      FieldType: {
        type: Number,
        default: 1
      },
      searchBy: {
        type: String, // No | Name | Both
        default: 'No'
      },
      getAll: {
        type: Boolean,
        default: false
      },
      clientDB: {
        type: Boolean,
        default: false
      },
      tdNoWith: {
        type: Number,
        default: 20
      },
      dropdownWith: {
        type: Number,
        default: 0
      },
      settings: [Array, Object],
      placeholder: {
        type: String,
        default: ''
      },
      url: {
        type: String,
        default: ''
      },
      multiple: {
        type: Boolean,
        default: false
      },
      allowClear: {
        type: Boolean,
        default: false
      },
      delay: {
        type: Number,
        default: 600
      },
      method: {
        type: String,
        default: 'post'
      },
      title: {
        type: String,
        default: ''
      }
    },
    data () {
      return {
        options: [],
        heightLight: false,
        isOpen: false,
        isSelect: false,
        styleTag: null,
        page: 0,
        perPage: 10,
        total: null,
        emptyClientDB: false,
        params: [],
        term: ''
      }
    },
    mounted: function () {
      let vm = this;
      document.addEventListener('keydown', function (e){
        if (e.keyCode === 40 || e.keyCode === 38) {
          vm.heightLight = true;
        } else {
          vm.heightLight = false;
        }
      }, true);
      $(this.$el)
        // init select2
        .select2(this.setConfig())
        .val(this.value)
        .trigger('change')
        // emit event on change.
        .on('change', function (e) {})
        .on('select2:open', function (e) {
          vm.isOpen = true;
          vm.isSelect = false;
          // set id for select2
          $('span.select2-container--open').attr('id', 'select2-container-' + vm._uid);
          $('span.select2-container--open').addClass('ijcore-select2-container-server2');
          if (vm.isOpen && !vm.multiple) {
            vm.$nextTick(() => {
              let $searchField = $('.select2-dropdown-' + vm._uid).find('.select2-search__field');
              if ($searchField && $searchField[0]) {
                $searchField[0].focus();
              }
            });
            if (vm.value[vm.FieldID] && vm.value[vm.FieldID] !== Number(this.value)) {
              $(vm.$el).val(vm.value[vm.FieldID]).trigger("change");
            }
          }

          if (vm.multiple) {
            if (vm.CoFieldID) {
              if (vm.value[vm.CoFieldID] !== Number(this.value)) {
                $(vm.$el).val(vm.value[vm.CoFieldID]).trigger("change");
              }
            }else {
              if (vm.value[vm.FieldID] && vm.value[vm.FieldID] !== Number(this.value)) {
                $(vm.$el).val(vm.value[vm.FieldID]).trigger("change");
              }
            }
          }

          const evt = "scroll.select2";
          $(e.target).parents().off(evt);
          $(window).off(evt);
          $('body').addClass('overflow-hidden');
        })
        .on('select2:opening', async function () {
          vm.page = 0;
          if (!vm.options.length) {
            try {
              // let value = await localforage.getItem('table.' + vm.table);
              // This code runs once the value has been loaded
              // from the offline store.
              vm.options = [];
              if (vm.getAll && !vm.options.length) {
                vm.$nextTick(() => {
                  $('.select2-results__option').text('Đang tải...');
                });
              }

              if (vm.getAll || vm.clientDB) {
                let value = [];
                let db = new Dexie("Listing");
                if (db) {
                  await db.open().then((db) => {
                  }).catch('NoSuchDatabaseError', function (e) {
                    // Database with that name did not exist
                    console.log('Database not found');
                  }).catch(function (e) {
                    console.log("Oh uh: " + e);
                  });
                  if (db._allTables && db._allTables[vm.table]) {
                    value = await db.table(vm.table).toArray();
                    if (!value || (value && !value.length)) {
                      await vm.getAllData();
                    } else {
                      if (vm.getAll) {
                        await vm.setOption(value);
                      }
                    }
                  } else {
                    await vm.getAllData();
                  }
                } else {
                  await vm.getAllData();
                }
              }
            } catch (err) {
              // This code runs if there were any errors.
              console.log(err);
            }
          }
        })
        .on('select2:closing', function (e){
          if (vm.multiple) {
            let $searchField = $(vm.$el).next('.select2').find('.select2-search__field');
            if ($searchField && _.isEmpty($searchField.val())) {
              vm.clearValue();
            }
          }
        })
        .on('select2:close', function (e) {
          vm.heightLight = false;
          vm.isOpen = false;
          $('body').removeClass('overflow-hidden');

          if (vm.CoFieldID) {
            if (vm.value[vm.CoFieldID]) {
              if (vm.multiple) {
                $(vm.$el).next('.select2').find('.select2-search__field').val(vm.value[vm.CoFieldNo]);
              }
            }
          } else {
            if (vm.value[vm.FieldID]) {
              if (vm.multiple) {
                $(vm.$el).next('.select2').find('.select2-search__field').val(vm.value[vm.FieldNo]);
              }
            }
          }

        })
        .on('select2:clear', function () {
          vm.clearValue();
        })
        .on('select2:selecting', function (e) {
          vm.isSelect = true;
        })
        .on('select2:select', function (e) {
          let itemSelected = e.params.data;
          if (vm.multiple) {
            $(vm.$el).next('.select2').find('.select2-search__field').val(itemSelected[vm.FieldNo]);
            $(vm.$el).val([itemSelected.id]).trigger("change");
          }
          if (itemSelected) {
            vm.updateValue(itemSelected);
          }
        })
        .on('select2:unselect', function (e) {
          if (vm.multiple) {
            $(vm.$el).val([]).trigger("change");
          }
        })
        .on('change change.select2 select2:closing select2:close select2:opening select2:open select2:selecting select2:select select2:unselecting select2:unselect select2:clearing select2:clear', function (e) {
          vm.$emit(e.type, e);
        });

      // set default
      if (vm.multiple) {
        let $search = $(vm.$el).next('.select2').find('.select2-search__field');
        if (vm.CoFieldID) {
          if (vm.FieldType === 2) {
            $search.val(vm.value[vm.CoFieldNo]);
          } else {
            $search.val(vm.value[vm.CoFieldName]);
          }
        }else {
          if (vm.FieldType === 2) {
            $search.val(vm.value[vm.FieldNo]);
          } else {
            $search.val(vm.value[vm.FieldName]);
          }
        }
      } else {

      }

      $('body').on({
        mouseenter: function (e) {
          // CODE
          setTimeout(() => {
            if (vm.heightLight && vm.isOpen && vm.multiple) {
              let $selected = $(e.currentTarget);
              let $selectedItem = $selected.find('.select2-item');
              let FieldID = $selectedItem.data('id');
              FieldID = Number(FieldID);
              let tmpSelected = _.find(vm.options, [vm.FieldID, FieldID]);
              let term = ($(vm.$el).next('.select2').find('.select2-search__field').val()) ? $(vm.$el).next('.select2').find('.select2-search__field').val() : '';
              let containNumber = term.match(/\d+/g);
              if (tmpSelected && (containNumber || term === '')) {
                $(vm.$el).next('.select2').find('.select2-search__field').val(tmpSelected[vm.FieldNo]);
              }
            }
          });
        }
      }, '.select2-results__option.select2-results__option--highlighted');

      // set with dropdown
      if (vm.dropdownWith && !vm.styleTag) {
        let css = `
            .ijcore-select2-server2.select2-dropdown-` + vm._uid + ` {
              width: ` + vm.dropdownWith + `px !important;
            }
            `;
        vm.styleTag = document.createElement('style');
        vm.styleTag.appendChild(document.createTextNode(css));
        document.head.appendChild(vm.styleTag);
      }

      // set value emptyClientDB

      let db = new Dexie("Listing");
      if (db) {
        db.open().then(async (db) => {
          if (db._allTables && db._allTables[vm.table]) {
            let total = await db.table(vm.table).toCollection().count();
            if (!total) {
              vm.emptyClientDB = true;
            }
          } else {
            vm.emptyClientDB = true;
          }
        }).catch('NoSuchDatabaseError', function (e) {
          // Database with that name did not exist
          console.log('Database not found');
        }).catch(function (e) {
          console.log("Oh uh: " + e);
        });
      }

    },
    methods: {
      setConfig(){
        let self = this;
        if (this.getAll) {
          let config = {
            data: this.options,
            allowClear: self.allowClear,
            multiple: self.multiple,
            placeholder: self.placeholder,
            cache: true,
            dropdownCssClass: 'ijcore-select2-server2 select2-dropdown-' + this._uid,
            containerCssClass: 'ijcore-select2-server2',
            matcher: function (params, data){
              // If there are no search terms, return all of the data
              if ($.trim(params.term) === '') {
                return data;
              }

              let containNumber = params.term.match(/\d+/g);
              if (containNumber == null) {
                // search by FieldName
                let FieldName = __.cleanAccents(data[self.FieldName]);
                FieldName = FieldName.toLowerCase();
                let search = __.cleanAccents(params.term);
                search = search.toLowerCase();
                if (FieldName.includes(search)) {
                  return data
                }

              }else {
                // search by FieldNo
                let regex = new RegExp('^' + params.term + '.*$');
                if (data[self.FieldNo] && data[self.FieldNo].match(regex)) {
                  // do something
                  return data;
                }
              }

              return null;
            },
            templateResult: this.templateResult,
            templateSelection: this.templateSelection
          };
          _.forEach(this.settings, function (value, key) {
            config[key] = value;
          });
          return config;
        }else {
          let url = self.url;
          if (self.clientDB) {
            url = self.$store.state.appRootApi + '/listing/api/client-db'
          }
          let config = {
            ajax: {
              headers: {
                Authorization: 'Bearer ' + TokenService.getToken()
              },
              method: self.method,
              dataType: 'json',
              cache: true,
              delay: self.delay,
              url: url,
              beforeSend : async function(xhr, data){
                if (self.clientDB) {
                  let db = new Dexie("Listing");
                  if (db) {
                    await db.open().then(async (db) => {
                    }).catch('NoSuchDatabaseError', function (e) {
                      // Database with that name did not exist
                      console.log('Database not found');
                    }).catch(function (e) {
                      console.log("Oh uh: " + e);
                    });

                    if (db._allTables && db._allTables[self.table]) {
                      let query = qs.parse(data.data);
                      let offset = self.page * self.perPage;
                      let regex = new RegExp('^' + query.term + '.*$');
                      let dataClient = await db.table(self.table).toCollection().filter(function (item){
                        if (query.term) {
                          let containNumber = query.term.match(/\d+/g);
                          if (containNumber == null) {
                            // search by FieldName
                            let FieldName = __.cleanAccents(item[self.FieldName]);
                            FieldName = FieldName.toLowerCase();
                            let search = __.cleanAccents(query.term);
                            search = search.toLowerCase();
                            if (FieldName.includes(search)) {
                              return true
                            }
                          }else {
                            // search by FieldNo
                            if (item[self.FieldNo] && item[self.FieldNo].match(regex)) {
                              // do something
                              return true;
                            }
                          }
                          return false;
                        }
                        return true;

                      }).offset(offset).limit(self.perPage).toArray();
                      let total = await db.table(self.table).toCollection().filter(function (item){
                        if (query.term) {
                          let containNumber = query.term.match(/\d+/g);
                          if (containNumber == null) {
                            // search by FieldName
                            let FieldName = __.cleanAccents(item[self.FieldName]);
                            FieldName = FieldName.toLowerCase();
                            let search = __.cleanAccents(query.term);
                            search = search.toLowerCase();
                            if (FieldName.includes(search)) {
                              return true
                            }
                          }else {
                            // search by FieldNo
                            if (item[self.FieldNo] && item[self.FieldNo].match(regex)) {
                              // do something
                              return true;
                            }
                          }
                          return false;
                        }
                        return true;

                      }).count();
                      self.total = total;

                      let options = [];
                      if (dataClient && dataClient.length) {
                        _.forEach(dataClient, function (value, key) {
                          let tmpObj = {};
                          tmpObj.id = value[self.FieldID];
                          tmpObj.text = value[self.FieldName];
                          if (self.FieldType === 2) {
                            tmpObj.text = value[self.FieldNo] + ' - ' + value[self.FieldName];
                          }
                          if (value[self.FieldID]) tmpObj[self.FieldID] = value[self.FieldID];
                          if (value[self.FieldNo]) tmpObj[self.FieldNo] = value[self.FieldNo];
                          if (value[self.FieldName]) tmpObj[self.FieldName] = value[self.FieldName];
                          if (self.FieldUpdate && self.FieldUpdate.length) {
                            _.forEach(self.FieldUpdate, function (field, key) {
                              tmpObj[field] = value[field];
                            });
                          }
                          options.push(tmpObj);
                        });
                      } else {
                        self.emptyClientDB = true;
                      }
                      self.options = options;
                    }else {
                      self.emptyClientDB = true;
                    }
                  }
                }
              },
              data: function (params) {
                if (self.term && (self.term !== params.term)) {
                  self.page = 0;
                }
                self.term = params.term;
                params.page = params.page || 1;
                return {
                  table: self.table,
                  FieldName: self.FieldName,
                  FieldNo: self.FieldNo,
                  FieldID: self.FieldID,
                  FieldWhere: self.FieldWhere,
                  FieldUpdate: self.FieldUpdate,
                  term: params.term, // search term
                  extraParams: self.extraParams,
                  clientDB: (self.clientDB) ? 1 : 0,
                  page: params.page,
                  per_page: 10
                }
              },
              processResults: function processResults(data, params) {
                if (self.clientDB) {
                  if (self.emptyClientDB && data.term) {
                    self.getDataFromServer(data.term);
                  }
                  self.page = params.page;
                  return {
                    results: self.options,
                    pagination: {
                      more: (self.options && self.options.length) ? params.page * self.perPage < self.total : false
                    }
                  };
                }else {
                  params.page = params.page || 1;
                  let options = [];
                  _.forEach(data.data.data, function (value, key) {
                    let tmpObj = {};
                    tmpObj.id = value[self.FieldID];
                    tmpObj.text = value[self.FieldName];
                    if (self.FieldType === 2) {
                      tmpObj.text = value[self.FieldNo] + ' - ' + value[self.FieldName];
                    }
                    if (value[self.FieldID]) tmpObj[self.FieldID] = value[self.FieldID];
                    if (value[self.FieldNo]) tmpObj[self.FieldNo] = value[self.FieldNo];
                    if (value[self.FieldName]) tmpObj[self.FieldName] = value[self.FieldName];
                    if (self.FieldUpdate && self.FieldUpdate.length) {
                      _.forEach(self.FieldUpdate, function (field, key) {
                        tmpObj[field] = value[field];
                      });
                    }
                    options.push(tmpObj);
                  });
                  self.options = options;
                  return {
                    results: options,
                    pagination: {
                      more: params.page * data.data.per_page < data.data.total
                    }
                  };
                }
              },
            },
            allowClear: self.allowClear,
            multiple: self.multiple,
            placeholder: self.placeholder,
            cache: true,
            dropdownCssClass: 'ijcore-select2-server2 select2-dropdown-' + this._uid,
            containerCssClass: 'ijcore-select2-server2',
            templateResult: this.templateResult,
            templateSelection: this.templateSelection
          };
          _.forEach(this.settings, function (value, key) {
            config[key] = value;
          });
          return config;
        }
      },
      async getAllData(){
        let self = this;
        let requestData = {
          method: 'post',
          data: {
            table: self.table,
            FieldName: self.FieldName,
            FieldNo: self.FieldNo,
            FieldID: self.FieldID,
            FieldWhere: self.FieldWhere,
            FieldUpdate: self.FieldUpdate,
            extraParams: this.extraParams,
            getAll: true,
          },
          url: this.url
        };
        ApiService.setHeader();
        await ApiService.customRequest(requestData).then(async (responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (self.getAll) {
              self.setOption(responsesData.data);
            }
            const db = new Dexie('Listing');
            let tableField = '++ID, ' + self.FieldID + ', ' + self.FieldNo + ', ' + self.FieldName;
            if (self.FieldUpdate && self.FieldUpdate.length) {
              _.forEach(self.FieldUpdate, function (fieldUpdate, key) {
                if (key !== (self.FieldUpdate.length - 1)) {
                  tableField += ', ';
                }
                tableField += fieldUpdate;
              });
            }

            await db.version(1).stores({[self.table]: tableField});
            await db.transaction('rw', self.table, async() => {
              _.forEach(responsesData.data, async function (value, key) {
                // Make sure we have something in DB:
                if ((await db[self.table].where({[self.FieldID]: value[self.FieldID]}).count()) === 0) {
                  let tmpObj = {};
                  tmpObj[self.FieldID] = value[self.FieldID];
                  tmpObj[self.FieldNo] = value[self.FieldNo];
                  tmpObj[self.FieldName] = value[self.FieldName];
                  if (self.FieldUpdate && self.FieldUpdate.length) {
                    _.forEach(self.FieldUpdate, function (fieldUpdate, key) {
                      tmpObj[fieldUpdate] = value[fieldUpdate];
                    });
                  }
                  await db[self.table].add(tmpObj).catch(function (e) {
                    console.log("Error: " + (e.stack || e));
                  });
                }
              });
            }).then(() => {
              $(this.$el).select2('close');
              let config = this.setConfig();
              $(this.$el).empty().select2(config);
              $(this.$el).select2('open');
            }).catch(e => {
              console.log(e.stack || e);
            });

          }
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      },
      setOption(responsesData){
        let self = this;
        self.options = [];
        _.forEach(responsesData, function (item, key) {
          let tmpObj = {};
          tmpObj.id = item[self.FieldID];
          tmpObj.text = item[self.FieldName];
          if (self.FieldType === 2) {
            tmpObj.text = item[self.FieldNo] + ' - ' + item[self.FieldName];
          }
          if (item[self.FieldID]) tmpObj[self.FieldID] = item[self.FieldID];
          if (item[self.FieldNo]) tmpObj[self.FieldNo] = item[self.FieldNo];
          if (item[self.FieldName]) tmpObj[self.FieldName] = item[self.FieldName];
          if (self.FieldUpdate && self.FieldUpdate.length) {
            _.forEach(self.FieldUpdate, function (field, key) {
              tmpObj[field] = item[field];
            });
          }
          self.options.push(tmpObj);
        });

        if (this.getAll) {
          let config = this.setConfig();
          $(this.$el).empty().select2(config);
          $(this.$el).select2('open');
        }
      },
      updateValue(tmpSelected){
        let vm = this;
        if (tmpSelected) {
          if (vm.CoFieldID) {
            if (tmpSelected[vm.FieldID]) vm.value[vm.CoFieldID] = tmpSelected[vm.FieldID];
            if (tmpSelected[vm.FieldNo]) vm.value[vm.CoFieldNo] = tmpSelected[vm.FieldNo];
            if (tmpSelected[vm.FieldName]) vm.value[vm.CoFieldName] = tmpSelected[vm.FieldName];
          }else {
            if (tmpSelected[vm.FieldID]) vm.value[vm.FieldID] = tmpSelected[vm.FieldID];
            if (tmpSelected[vm.FieldNo]) vm.value[vm.FieldNo] = tmpSelected[vm.FieldNo];
            if (tmpSelected[vm.FieldName]) vm.value[vm.FieldName] = tmpSelected[vm.FieldName];
          }
          if (vm.FieldUpdate && vm.FieldUpdate.length) {
            _.forEach(vm.FieldUpdate, function (field, key) {
              vm.value[field] = tmpSelected[field];
            });
          }
          if (vm.FieldUpdateSpecial) {
            _.forEach(vm.FieldUpdateSpecial, function (field, key) {
              vm.value[key] = tmpSelected[field];
            });
          }
        }
        this.$emit('selected', tmpSelected);
      },
      templateSelection(state){
        if (!state.id) {
          if (!this.value[this.FieldID]) {
            return state.text;
          }else {
            let html = '<span style="color: #151b1e">' + this.value[this.FieldName] + '</span>';
            return $(html);
          }
        }
        let html = '';
        if (this.FieldType === 2) {
          html = '<span>' + state[this.FieldNo] + '</span>';
        }else {
          html = '<span>' + state[this.FieldName] + '</span>';
        }
        let $html = $(html);
        return $html;
      },
      templateResult(state){
        if (this.FieldType === 2) {
          let html = '<table style="width:100%" class="select2-item select2-table" data-id="' + state.id + '">' +
            '<tr>' +
            '<td style="width: ' + this.tdNoWith + '%">' + state[this.FieldNo] + '</td>' + '<td>' + state[this.FieldName] + '</td>' +
            '</tr>' +
            '</table>';
          let $html = $(html);
          return $html;
        }else {
          let html = '<span>' + state.text + '</span>';
          let $html = $(html);
          return $html;
        }
      },
      clearValue(){
        let self = this;
        if (self.CoFieldID) {
          self.value[self.CoFieldID] = null;
          self.value[self.CoFieldNo] = '';
          self.value[self.CoFieldName] = '';
        }else {
          self.value[self.FieldID] = null;
          self.value[self.FieldNo] = '';
          self.value[self.FieldName] = '';
        }
        if (self.FieldUpdate && self.FieldUpdate.length) {
          _.forEach(self.FieldUpdate, function (field, key) {
            self.value[field] = null;
          });
        }
      },
      async getDataFromServer(term) {
        let self = this;
        let requestData = {
          method: 'post',
          data: {
            table: self.table,
            FieldName: self.FieldName,
            FieldNo: self.FieldNo,
            FieldID: self.FieldID,
            FieldWhere: self.FieldWhere,
            FieldUpdate: self.FieldUpdate,
            extraParams: this.extraParams,
            emptyClientDB: self.emptyClientDB,
            clientDB: self.clientDB,
            term: term,
            getAll: true,
          },
          url: this.url
        };
        ApiService.setHeader();
        await ApiService.customRequest(requestData).then(async (responses) => {
          let responsesData = responses.data;
          if (responsesData.status === 1) {
            if (responsesData.data && responsesData.data.length) {
              const db = new Dexie('Listing');
              let tableField = '++ID, ' + self.FieldID + ', ' + self.FieldNo + ', ' + self.FieldName;
              if (self.FieldUpdate && self.FieldUpdate.length) {
                _.forEach(self.FieldUpdate, function (fieldUpdate, key) {
                  if (key !== (self.FieldUpdate.length - 1)) {
                    tableField += ', ';
                  }
                  tableField += fieldUpdate;
                });
              }
              await db.version(1).stores({[self.table]: tableField});
              await db.transaction('rw', self.table, async() => {
                _.forEach(responsesData.data, async function (value, key) {
                  // Make sure we have something in DB:

                  if ((await db[self.table].where({[self.FieldID]: value[self.FieldID]}).count()) === 0) {
                    let tmpObj = {};
                    tmpObj[self.FieldID] = value[self.FieldID];
                    tmpObj[self.FieldNo] = value[self.FieldNo];
                    tmpObj[self.FieldName] = value[self.FieldName];
                    if (self.FieldUpdate && self.FieldUpdate.length) {
                      _.forEach(self.FieldUpdate, function (fieldUpdate, key) {
                        tmpObj[fieldUpdate] = value[fieldUpdate];
                      });
                    }
                    await db[self.table].add(tmpObj).catch(function (e) {
                      console.log("Error: " + (e.stack || e));
                    });
                  }
                });
              }).then(() => {
                $(this.$el).select2('close');
                if (self.term) {
                  $(self.$el).select2('search', self.term);
                  self.emptyClientDB = false;
                }
              }).catch(e => {
                // alert(e.stack || e);
                console.log(e.stack || e);
              });
            }
          }
          self.emptyClientDB = false;
          self.$store.commit('isLoading', false);
        }, (error) => {
          console.log(error);
          self.$store.commit('isLoading', false);
        });
      }
    },
    watch: {
      value: {
        handler(oldVal){
          // do stuff
          if (!this.isSelect) {
            if (this.CoFieldID || this.CoFieldNo) {
              if (this.value[this.CoFieldID] || this.value[this.CoFieldNo]) {
                $(this.$el).val(this.value[this.CoFieldID]).trigger("change");
                if (this.multiple) {
                  $(this.$el).next('.select2').find('.select2-search__field').val(this.value[this.CoFieldNo]);
                }
              }
            } else {
              if (this.value[this.FieldID] || this.value[this.FieldNo]) {
                $(this.$el).val(this.value[this.FieldID]).trigger("change");
                if (this.multiple) {
                  $(this.$el).next('.select2').find('.select2-search__field').val(this.value[this.FieldNo]);
                }
              }
            }
          }
        },
        deep: true
      },
      '$props.table'() {
        this.options = [];
        let config = this.setConfig();
        $(this.$el).empty().select2(config);
      }
    },
    destroyed: function () {
      $(this.$el).off().select2('destroy');
      if (this.styleTag) {
        this.styleTag.remove();
      }
    }
  }
</script>
