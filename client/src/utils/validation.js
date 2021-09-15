export function atLeastOneValidator(data){
    if (data.length === 0){
      return {error: true, message: "Pick at least one"}
    }else{
      return {error: false, message: ""}
    }
  }
  
export function notEmptyValidator(data){
    if (!data) {
      return {error: true, message: "Required"}
    } else{
      return {error: false, message: ""}
    }
  }