import moment from 'moment';

const filters = {
  capitalize(value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
  },
  convertServerDateToClientDate(date) {
    if (!date) return;
    let checkFormat = moment(date, 'DD/MM/YYYY', true).isValid();
    if (!checkFormat) {
      return moment(date).format('DD/MM/YYYY');
    }
    return date;
  },
  convertTimeFromDatetime(datetime) {
    if (!datetime) return;
    let checkFormat = moment(datetime, 'HH:mm:ss', true).isValid();
    if (!checkFormat) {
      return moment(datetime).format('HH:mm:ss');
    }
    return datetime;
  },
  convertTimeToDateName(datetime) {
    if (!datetime) return;
    let result = moment(datetime).format('dddd, DD/MM/YYYY');
    result = result.toString();
    return result.charAt(0).toUpperCase() + result.slice(1);
  },
  convertTimeToTimeAgo(datetime) {
    if (!datetime) return;
    return moment(datetime).fromNow();
  },
  convertTimeToHMTime(datetime) {
    if (!datetime) return;
    let checkFormat = moment(datetime, 'DD/MM/YYYY HH:mm', true).isValid();
    if (!checkFormat) {
      return moment(datetime).format('DD/MM/YYYY HH:mm');
    }
    return datetime;
  },
  /**
   * Returns the text from a HTML string
   *
   * @param {html} String The html string
   */
  stripHtml(html) {
    // Create a new div element
    let temporalDivElement = document.createElement("div");
    // Set the HTML content with the providen
    temporalDivElement.innerHTML = html;
    // Retrieve the text property of the element (cross-browser support)
    return temporalDivElement.textContent || temporalDivElement.innerText || "";
  },

  convertNumberToText(number) {
    if (number) {
      let arrTemp = number.toString().split('.');
      for (let i = 0; i < arrTemp.length; i++) {
        let val = arrTemp[i];
        let length = val.length;
        let textNumber = '';
        let number = 3;
        if (i == 0) {
          for (let j = 0; j < length; j++) {
            if (j == number) {
              textNumber = val[length - j - 1] + '.' + textNumber;
              number = number + 3;
            } else {
              textNumber = val[length - j - 1] + '' + textNumber;
            }
          }

          arrTemp[i] = textNumber;
        } else {
          textNumber = val.replace(/^|0+$/g, "");
          // textNumber = parseFloat(textNumber).toString();
          if (textNumber === '0') {
            arrTemp.splice(i, 1);
          } else {
            arrTemp[i] = textNumber;
          }
        }

      }
      return arrTemp.join(',');
    } else {
      return '';
    }
  },
  formatCurrency(number) {
    if (number) {
      let arrTemp = number.toString().split('.');
      for (let i = 0; i < arrTemp.length; i++) {
        let val = arrTemp[i];
        let length = val.length;
        let textNumber = '';
        let number = 3;
        if (i == 0) {
          for (let j = 0; j < length; j++) {
            if (j == number) {
              textNumber = val[length - j - 1] + '.' + textNumber;
              number = number + 3;
            } else {
              textNumber = val[length - j - 1] + '' + textNumber;
            }
          }

          arrTemp[i] = textNumber;
        } else {
          textNumber = val.replace(/^|0+$/g, "");
          // textNumber = parseFloat(textNumber).toString();
          if (textNumber === '0') {
            arrTemp.splice(i, 1);
          } else {
            arrTemp[i] = textNumber;
          }
        }

      }
      return arrTemp.join(',');
    } else {
      return '';
    }
  },
  formatPercent(number){
    number = number * 100;
    number = number.toFixed(2);
    number = filters.convertNumberToText(number);
    return number;
  },
  perView: function (value, per, field) {
    let AccessField = ','+per['AccessField']+',';
    if(per['AccessField'] == 'all' || AccessField.includes(','+field+',')){
      return value;
    }else{
      return '';
    }
  },
  perEdit: function (value, per, field) {
    let EditField = ','+per['EditField']+',';
    if(per['EditField'] == 'all' || EditField.includes(','+field+',')){
      return true;
    }else{
      return false;
    }
  }
};

export default filters;
