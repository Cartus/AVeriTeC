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
  if (["yes", "no", "true", "false", "yea", "yeah", "nay", "y", "n"].includes(data.toLowerCase())) {
    return { error: true, message: "Wrong type for boolean answer" }
  } else {
    return { error: false, message: "" }
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