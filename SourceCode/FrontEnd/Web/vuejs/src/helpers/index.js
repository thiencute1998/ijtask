import _ from 'lodash';
import moment from 'moment';
import Dexie from "dexie";

function renderErrorsApiHtml(errors) {
  let html = '';
  _.forEach(errors, function (error, key) {
    html += '<p>' + error + '</p>';
  });

  return html;
}

function renderErrorApiHtml(errors) {
  let html = '';
  _.forEach(errors, function (error, key) {
    html += '<p>' + error + '</p>';
    return false;
  });
  return html;
}

function renderErrorApiHtmlObject(errors) {
  let html = '';
  _.forEach(errors, function (error, key) {
    html += '<p>' + error + '</p>';
  });
  return html;
}

function insertBeforeKey(arr, index, newItem) {
  return [
    ...arr.slice(0, index),

    newItem,

    ...arr.slice(index)
  ];
}

/**
 * Returns the text from a HTML string
 *
 * @param {html} String The html string
 */
function stripHtml(html) {
  // Create a new div element
  let temporalDivElement = document.createElement("div");
  // Set the HTML content with the providen
  temporalDivElement.innerHTML = html;
  // Retrieve the text property of the element (cross-browser support)
  return temporalDivElement.textContent || temporalDivElement.innerText || "";
}

/**
 *
 * @param event
 * @desc hiển thị submenu trong dropdown
 */
function onToggleDropdownSubMenu(event) {
  let dropdownMenu = event.target.nextElementSibling;
  let dropdownMenuItems = dropdownMenu.childNodes;
  let allDropdownMenu = document.querySelectorAll('.dropdown-sub-menu');
  if (dropdownMenu.classList.contains('show')) {
    dropdownMenu.classList.remove('show');
  } else {
    for (let j = 0; j < allDropdownMenu.length; j++) {
      allDropdownMenu[j].classList.remove('show');
    }
    $(dropdownMenu).parents('.dropdown-sub-menu').addClass('show');
    dropdownMenu.classList.add('show');
  }

  for (let i = 0; i < dropdownMenuItems.length; i++) {
    dropdownMenuItems[i].addEventListener('click', function () {
      for (let j = 0; j < allDropdownMenu.length; j++) {
        allDropdownMenu[j].classList.remove('show');
      }
    });
  }

}


function convertDate(date) {
  if (!date) return;
  return moment(date).format('DD/MM/YYYY');
}

function convertDateTime(date) {
  if (!date) return;
  return moment(date).format('DD/MM/YYYY HH:mm:ss');
}

function convertDateToString(date) {
  if (!date) return;
  return moment(date).format('DD/MM/YYYY');
}

function convertDateTimeToString(date) {
  if (!date) return;
  return moment(date).format('DD/MM/YYYY HH:mm:ss');
}

function convertServerDateToClientDate(date) {
  if (!date) return;
  return moment(date).format('DD/MM/YYYY');
}

function convertTimeFromDatetime(datetime) {
  if (!datetime) return;
  return moment(datetime).format('HH:mm:ss');
}

function convertTextToNumber(text) {
  if (!text) {
    return;
  }
  let arr = text.split(',');
  if (arr[0]) {
    text = arr[0].replace(/[^\d]/g, '');
  }
  if (arr[1]) {
    text += '.' + arr[1];
  }
  return Number(text);
}

function convertNumberToText(number) {
  if (number) {
    let arrTemp = number.toString().split('.');
    for (var i = 0; i < arrTemp.length; i++) {
      var val = arrTemp[i];
      var length = val.length;
      var textNumber = '';
      var number = 3;
      if (i == 0) {
        for (var j = 0; j < length; j++) {
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
}
function perViewColumn(per, field) {
  var AccessField = ','+per['AccessField']+',';
  if(per['AccessField'] == 'all' || AccessField.includes(','+field+',')){
    return true;
  }else{
    return false;
  }
}
function perEditColumn(per, field){
  var EditField = ','+per['EditField']+',';
  if(per['EditField'] == 'all' || EditField.includes(','+field+',')){
    return true;
  }else{
    return false;
  }
}
function perDeleteColumn(per){
  if(per['Delete'] == 1){
    return true;
  }else{
    return false;
  }
}
function checkPeriod(PeriodType, FromDate, ToDate) {
  var ArrFromDate = FromDate.split("/");
  var ArrToDate = ToDate.split("/");
  if(ArrFromDate[2] == undefined || ArrToDate[2] == undefined){
    return false;
  }
  switch (parseInt(PeriodType)) {
    case 1:
      if(FromDate != '01/01/'+ArrToDate[2] || '31/12/'+ArrFromDate[2] != ToDate){
        return false;
      }else{
        return true;
      }
      break;
    case 2:
      if(parseInt(ArrFromDate[1]) <= 6){
        var d = new Date(ArrToDate[2]+'-07-01');
        d.setDate(d.getDate()-1);
        var TempToDate = moment(d).format('DD/MM/YYYY');
        if(FromDate != '01/01/'+ArrToDate[2] || TempToDate != ToDate){
          return false;
        }else{
          return true;
        }
      }else{
        if(FromDate != '01/07/'+ArrToDate[2] || '31/12/'+ArrFromDate[2] != ToDate){
          return false;
        }else{
          return true;
        }
      }
      break;
    case 3:
      var Quarter = Math.floor(parseInt(ArrFromDate[1])/3);
      var FromMonthQuarter = Quarter*3 + 1;
      var ToMonthQuarter = Quarter*3 + 3;
      if(ToMonthQuarter + 1 < 10){
        ToMonthQuarter = ToMonthQuarter + 1;
        ToMonthQuarter = '0'+ToMonthQuarter
      }else{
        ToMonthQuarter = ''+ToMonthQuarter;
      }
      if(FromMonthQuarter < 10){
        FromMonthQuarter = '0'+FromMonthQuarter
      }else{
        FromMonthQuarter = ''+FromMonthQuarter;
      }
      var d = new Date(ArrToDate[2]+'-'+ToMonthQuarter+'-01');
      d.setDate(d.getDate()-1);
      var TempToDate = moment(d).format('DD/MM/YYYY');
      if(FromDate != '01/'+FromMonthQuarter+'/'+ArrToDate[2] || TempToDate != ToDate){
        return false;
      }else{
        return true;
      }
      break;
    case 4:
      var TempToMonth = Math.floor(parseInt(ArrFromDate[1])) + 1;
      if(TempToMonth < 10){
        TempToMonth = '0'+TempToMonth
      }else{
        TempToMonth = ''+TempToMonth;
      }
      var d = new Date(ArrFromDate[2]+'-'+TempToMonth+'-01');
      d.setDate(d.getDate()-1);
      var TempToDate = moment(d).format('DD/MM/YYYY');
      if(FromDate != '01/'+ArrToDate[1]+'/'+ArrToDate[2] || TempToDate != ToDate){
        return false;
      }else{
        return true;
      }
      break;
    case 5:
      var d = new Date(ArrFromDate[2]+'-01-01');
      var FromDate = new Date(ArrFromDate[2]+'-'+ArrFromDate[1]+'-'+ArrFromDate[0]);
      var ToDate = new Date(ArrToDate[2]+'-'+ArrToDate[1]+'-'+ArrToDate[0]);
      var dayOfWeekFirst = d.getDay();
      var NumberDayWeekFirst = 8 - dayOfWeekFirst;
      if((Math.round((FromDate-d)/(1000*60*60*24)) + 1 - NumberDayWeekFirst)%7 == 1 && (ToDate-FromDate)/(1000*60*60*24) == 6){
        return true;
      }else{
        return false;
      }
      break;
    case 6:
      if(FromDate != ToDate){
        return false;
      }else{
        return true;
      }
      break;
    default:
      return true;
      break;
  }
}
function cleanAccents(str) {
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
  str = str.replace(/đ/g, "d");
  str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
  str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
  str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
  str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
  str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
  str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
  str = str.replace(/Đ/g, "D");
  return str;
}
function stringToSlug(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim
  str = str.toLowerCase();

  // remove accents, swap ñ for n, etc
  var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
  var to   = "aaaaeeeeiiiioooouuuunc------";
  for (var i=0, l=from.length ; i<l ; i++) {
    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
  }

  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    .replace(/-+/g, '-'); // collapse dashes

  return str;
}
function createSchemaClientDB() {
  const db = new Dexie('Listing');
  db.version(1).stores({
    employee: "++ID, EmployeeID, EmployeeNo, EmployeeName",
    coa_con: "++ID, AccountID, AccountNo, AccountName", //Hợp nhất
    coa_anu: "++ID, AccountID, AccountName, AccountNo", //Đơn vị HCSN
    coa_pmu: "++ID, AccountID, AccountName, AccountNo", //Ban QLDA
    coa_tab: "++ID, AccountID, AccountName, AccountNo", //Kho bạc Nhà nước
    coa_sna: "++ID, AccountID, AccountName, AccountNo", //Tài Khoản Quốc gia
    coa_scb: "++ID, AccountID, AccountName, AccountNo", //Xã phường
    ccy: "++ID, CcyID, CcyName, CcyNo",
    item: "++ID, ItemID, ItemName, ItemNo",
    fixed_asset: "++ID, FixedAssetID, FixedAssetName, FixedAssetNo",
    tool: "++ID, ToolID, ToolName, ToolNo",
    invest_asset: "++ID, InvestAssetID, InvestAssetName, InvestAssetNo",
    uom: "++ID, UomID, UomName, UomNo"
  });

  const dbReport = new Dexie('Report');
  dbReport.version(1).stores({
    rpt_report_para: "++ID, LineID, ReportID, ReportName, ParaID, ParaName, ParaValue, ParaValueID, ParaValueName, ParaValueNo, ParaKey, NOrder",
    rpt_para: "++ID, NOrder, DataType, ParaKey, ParaName, ParaType, Inactive"
  });

  db.open();
  dbReport.open();
}
export default {
  renderErrorsApiHtml,
  renderErrorApiHtml,
  renderErrorApiHtmlObject,
  onToggleDropdownSubMenu,
  insertBeforeKey,
  stripHtml,
  convertDate,
  convertDateToString,
  convertServerDateToClientDate,
  convertTimeFromDatetime,
  convertDateTimeToString,
  convertDateTime,
  convertTextToNumber,
  convertNumberToText,
  perViewColumn,
  perEditColumn,
  perDeleteColumn,
  checkPeriod,
  cleanAccents,
  stringToSlug,
  createSchemaClientDB
};
