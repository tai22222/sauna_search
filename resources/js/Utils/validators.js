export const isValidEmail = email => {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  if(re.test(String(email).toLowerCase())){
    return {isValid: true};
  } else {
    return {
      isValid: false,
      errorMessage: "無効なメールアドレスです。"
    }
  }
};

// パスワードのバリデーション
export const isValidPassword = password => {
  if(password.length >= 8){
    return {isValid: true};
  } else {
    return {
      isValid: false,
      errorMessage: "パスワードは8文字以上で入力してください。"
    }
  }
};

// パスワードの一致確認
export const doPasswordsMatch = (password, confirmPassword) => {
  if( password === confirmPassword) {
    return { isValid: true}
  } else {
    return {
      isValid: false,
      errorMessage: "パスワードが一致しません。"
    }
  }
};

// 電話番号のバリデーション
export const isValidPhone = phoneNumber => {
  // ハイフンを削除
  const digits = phoneNumber.replace(/-/g, '');
  const re = /^\d{10,11}$/; // 日本の電話番号を想定
  if( re.test(digits)){
    return { isValid: true}
  } else {
    return {
      isValid: false,
      errorMessage: "正式な電話番号を入力してください。"
    }
  }
};


// テキスト入力のバリデーション(最低文字数&最大文字)
export const isValidText = (text, max, min) => {
  if(text.trim().length > min && text.trim().length <= max) {
    return { isValid: true};
  } else {
    return {
      isValid: false,
      errorMessage: min + "~" + max + "文字で記入してください"
    }
  }
};

// 最大文字数のバリデーション(必須ではない場合使用)
export const isValidMax = (text, max) => {
  if(text.trim().length <= max) {
    return {isValid: true};
  } else {
    return {
      isValid: false,
      errorMessage: max + "文字以内での入力をしてください。"
    }
  }
}

// URLのバリデーション
export const isValidUrl = url => {
  try {
    new URL(url);
    return {isValid: true};
  } catch (_) {
    return {
      isValid: false,
      errorMessage: "有効なURLを指定してください。"
    };
  }
};

// 数値のバリデーション
export const isValidNumber = number => {
  if(!isNaN(number)){
    return {isValid: true}
  } else {
    return {
      isValid: false,
      errorMessage: "半角数字を入力してください"
    }
  }
};

// 画像サイズのバリデーション（例：2MB未満）
export const isValidImageSize = imageFile => {
  const sizeInMB = imageFile.size / 1024 / 1024;
  if( sizeInMB < 2) {
    return { isValid: true};
  } else {
    return {
      isValid: false,
      errorMessage: "ファイルサイズが大きすぎます。"
    }
  }
};

// 画像形式のバリデーション（例：JPEG, PNG）
export const isValidImageType = imageFile => {
  const validTypes = ['image/jpeg', 'image/png'];
  if(validTypes.includes(imageFile.type)) {
    return { isValid: true};
  } else {
    return {
      isValid: false,
      errorMessage: "許可されていないファイルタイプです"
    }
  }
};

