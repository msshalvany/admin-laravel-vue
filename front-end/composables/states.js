
export const basUrl = () => useState('basUrl', () => 'http://localhost:8000/api' )
export const useLoader = () => useState('loader', () => false )
export const useAlertStateSuccess = () => useState('useAlertStateSuccess', () => false )
export const useAlertStateError= () => useState('useAlertStateError', () => false )
export const useAlertStateWarning = () => useState('useAlertStateWarning', () => false )
export const useAlertText = () => useState('useAlertText', () => 'salam' )
