import ApiService from '@/services/api.service';
export default {
  methods:{
    $_tableTree_onDeleteFieldOnTable(key, nameProperty, keyTable) {
      let self = this;

      if (this.model[nameProperty][key].Detail === 1) {
        if (this.model[nameProperty][key].ParentID) {
          let siblings = _.filter(this.model[nameProperty], ['ParentID', this.model[nameProperty][key].ParentID]);
          if (siblings && siblings.length === 1) {
            let indexParent = _.findIndex(this.model[nameProperty], [keyTable, this.model[nameProperty][key].ParentID]);
            if (indexParent > -1) {
              this.model[nameProperty][indexParent].Detail = 1;
              this.model[nameProperty][indexParent].HaveChildren = false;
            }
          }
        }
      }else {

        // lui cap
        let allChildTableItem = this.$_tableTree_getAllChildTableItem(this.model[nameProperty][key], this.model[nameProperty], keyTable);

        _.forEach(allChildTableItem, function (item, keyItem) {
          let indexItem = _.findIndex(self.model[nameProperty], [keyTable, item[keyTable]]);
          if (indexItem > -1) {
            self.model[nameProperty][indexItem].Level -= 1;
          }
        });

        // gan lai cha cho con
        let children = _.filter(this.model[nameProperty], ['ParentID', this.model[nameProperty][key][keyTable]]);
        _.forEach(children, function (sonny, keySonny) {
          let indexSonny = _.findIndex(self.model[nameProperty], [keyTable, sonny[keyTable]]);
          if (indexSonny > -1) {
            self.model[nameProperty][indexSonny].ParentID = self.model[nameProperty][key].ParentID;
          }
        });

      }
      this.model[nameProperty].splice(key, 1);
    },
    $_tableTree_getAllChildTableItem(item, tableItemArr, keyTable){
      let self = this, listChild = [];
      let allChild = _.filter(tableItemArr, ['ParentID', item[keyTable]]);
      if (allChild.length) {
        allChild = _.orderBy(allChild, [keyTable], ['asc']);
        _.forEach(allChild, function (value, key) {
          listChild.push(value);
          if (_.filter(tableItemArr, ['ParentID', value[keyTable]]).length) {
            let recursiveArr = self.$_tableTree_getAllChildTableItem(value, tableItemArr, keyTable);
            recursiveArr = _.orderBy(recursiveArr, [keyTable, 'asc']);
            _.forEach(recursiveArr, function (recursive, key) {
              listChild.push(recursive);
            });
          }
        });
      }
      return listChild;
    },
    $_tableTree_sumpUp(nameProperty, keyTable, sumArr) {
      let self = this;
      let maxLevel = _.maxBy(this.model[nameProperty], 'Level');
      if (maxLevel) {
        for (let i = maxLevel.Level; i > 0; i--) {
          let tmpSumUPItems = _.filter(self.model[nameProperty], ['Level', i]);
          if (tmpSumUPItems && tmpSumUPItems.length) {
            const sumParents = _(tmpSumUPItems)
              .groupBy('ParentID')
              .map((item, ParentID) => ({
                [keyTable]: ParentID,
                FCAmount: _.sumBy(item, 'FCAmount'),
                LCAmount: _.sumBy(item, 'LCAmount')}
              ))
              .value();

            if (sumParents && sumParents.length) {
              _.forEach(sumParents, function (sumParent, key) {
                if (sumParent[keyTable]) {
                  let sumItemIndex = _.findIndex(self.model[nameProperty], [keyTable, Number(sumParent[keyTable])]);
                  if (sumItemIndex > -1) {
                    self.model[nameProperty][sumItemIndex].FCAmount = sumParent.FCAmount;
                    self.model[nameProperty][sumItemIndex].LCAmount = self.model[nameProperty][sumItemIndex].FCAmount * self.model[nameProperty][sumItemIndex].ExchangeRate;
                  }
                }
              });
            }
          }
        }
      }
      this.$bvToast.toast('Cập nhật thành công', {
        variant: 'success',
        title: 'Thông báo',
        solid: true
      });
    }
  },
  watch:{}
}
