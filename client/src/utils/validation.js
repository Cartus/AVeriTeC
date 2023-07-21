
import isUrl from "is-url"

export function atLeastOneValidator(data) {
  if (data.length === 0) {
    return { error: true, message: "Pick at least one" }
  } else {
    return { error: false, message: "" }
  }
}

export function notEmptyValidator(data) {
  if (!data) {
    return { error: true, message: "Required" }
  } else {
    return { error: false, message: "" }
  }
}

export function notBooleanValidator(data) {
  if (["yes", "no", "true", "false", "yea", "yeah", "nay", "y", "n", "nope", "aye", "nah"].includes(data.toLowerCase())) {
    return { error: true, message: "Wrong type for boolean answer" }
  } else {
    return { error: false, message: "" }
  }
}

export function emptyOrValidUrlValidator(data){
  if (data && !isUrl(data) && !isUrl("http://" + data)){
    return { error: true, message: "Not a valid URL" };  
  }

  return { error: false, message: "" }
}

export function noUrlOverlapValidator(blocked_url) {
  // Split bloocked url at https:// or http://
  let blocked_url_split = blocked_url.split(/(https?:\/\/)/g)

  // Take the last element of the blocked url
  blocked_url_split = blocked_url_split[blocked_url_split.length - 1]

  return (data) => {
    console.log(data)
    console.log(blocked_url_split)
    console.log(data.includes(blocked_url_split))
    if (data && blocked_url_split && data.includes(blocked_url_split)) {
      console.log("URL overlaps with blocked URL")
      return { error: true, message: "URL overlaps with blocked URL" }
    } else {
      return { error: false, message: "" }
    }
  }
}

export function combinedValidator(validator1, validator2) {
  return (data) => {
    let v1 = validator1(data)
    let v2 = validator2(data)

    if (v1.error) {
      return v1
    } else if (v2.error) {
      return v2
    } else {
      return { error: false, message: "" }
    }
  }
}