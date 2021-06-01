import { /*Tooltip,*/ Toast/*, Popover*/} from 'bootstrap';

export default class ToastComponent {
    constructor(title, type, bg, icon)
    {   
        this.icon = icon;
        this.title = title;
        this.bg = bg;
        this.type = type;
        this.appendToastinBody();
    }

    toastContent() {
        return `
        <div style="position: fixed; bottom: 0px; right: 5px; z-index: 10;" 
        class="toast ${ this.type } ${ this.bg } text-white" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="toast-header">
            <i class="fas ${ this.icon } text-secondary fa-2x"></i>
            <strong class="me-auto">Symfony App</strong>
            <small class="text-muted">1 mins ago</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            ${ this.title }
          </div>
        </div>
        `;
    }

    appendToastinBody() {
       const container = document.querySelector('div#toasts');
       container.innerHTML = this.toastContent();
    }

    show(){
        const toastDiv = document.querySelector(`.${this.type}`);
        const toast = new Toast(toastDiv);
        toast.show();
    }
}