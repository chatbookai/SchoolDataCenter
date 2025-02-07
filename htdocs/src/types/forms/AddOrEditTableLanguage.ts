interface AddOrEditTableLanguageType{
    [key:string] : any
}

const mixed:AddOrEditTableLanguageType = {
  default: '${path} 必须是一个有效的值',
  required: '${path} 必须填写',
  oneOf: '${path} 必须是以下值中的一个: ${values}',
  notOneOf: '${path} 不能是以下值的任何一个: ${values}',
};

const string:AddOrEditTableLanguageType = {
  length: '${path} 长度必须是 ${length} 个字符',
  min: '${path} 长度至少是 ${min} 个字符',
  max: '${path} 长度最多是 ${max} 个字符',
  matches: '${path} 必须满足以下规则: "${regex}"',
  email: '${path} 必须是一个合法的电子邮件',
  url: '${path} 必须是一个合法的网址',
  trim: '${path} 字符两头不能有空格',
  lowercase: '${path} 必须是小写字母',
  uppercase: '${path} 必须是大写字母',
};

const number:AddOrEditTableLanguageType = {
  min: '${path} 必须大于或等于 ${min}',
  max: '${path} 必须小于或等于 ${max}',
  lessThan: '${path} 必须小于 ${less}',
  moreThan: '${path} 必须大于 ${more}',
  notEqual: '${path} 必须不等于 ${notEqual}',
  positive: '${path} 必须是一个正数',
  negative: '${path} 必须是一个负数',
  integer: '${path} 必须是一个整数',
};

const date:AddOrEditTableLanguageType = {
  min: '${path} 值必须在这个之后 ${min}',
  max: '${path} 值必须在这个之前 ${max}',
};

const boolean:AddOrEditTableLanguageType = {};

const object:AddOrEditTableLanguageType = {
  noUnknown: '${path} field cannot have keys not specified in the object shape',
};

const array:AddOrEditTableLanguageType = {
  min: '${path} 至少拥有 ${min} 个值',
  max: '${path} 至多拥有 ${max} 个值',
};

const AddOrEditTableLanguage:AddOrEditTableLanguageType = {
  mixed,
  string,
  number,
  date,
  object,
  array,
  boolean,
};

export default AddOrEditTableLanguage