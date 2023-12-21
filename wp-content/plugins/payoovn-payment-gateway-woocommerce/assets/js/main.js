class PayooBank extends HTMLElement {
    constructor() {
      super();
    }

    connectedCallback() {
      this.addInputSearch()
    }
   
    addInputSearch() {
      let searchDiv = document.createElement('div')
      searchDiv.className = 'payoo-bank-search';
      let icon =  document.createElement('img')
      icon.src = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTkuMTkzNDkgMy4yNDUyMUMxMi40Nzg5IDMuMjQ1MjEgMTUuMTQxOCA1LjkxNzYyIDE1LjE0MTggOS4xOTM0OUMxNS4xNDE4IDEyLjQ3ODkgMTIuNDY5MyAxNS4xNDE4IDkuMTkzNDkgMTUuMTQxOEM1LjkwODA1IDE1LjE0MTggMy4yNDUyMSAxMi40NjkzIDMuMjQ1MjEgOS4xOTM0OUMzLjI0NTIxIDUuOTE3NjIgNS45MTc2MiAzLjI0NTIxIDkuMTkzNDkgMy4yNDUyMVpNOS4xOTM0OSAyQzUuMjE4MzkgMiAyIDUuMjE4MzkgMiA5LjE5MzQ5QzIgMTMuMTY4NiA1LjIxODM5IDE2LjM4NyA5LjE5MzQ5IDE2LjM4N0MxMy4xNjg2IDE2LjM4NyAxNi4zODcgMTMuMTY4NiAxNi4zODcgOS4xOTM0OUMxNi4zOTY2IDUuMjE4MzkgMTMuMTY4NiAyIDkuMTkzNDkgMloiIGZpbGw9IiM3MDcwNzAiLz4KPHBhdGggZD0iTTE5IDE5TDE0IDE0IiBzdHJva2U9IiM3MDcwNzAiIHN0cm9rZS13aWR0aD0iMS4zIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+Cg=="
     
      let search = document.createElement('input')

      let notfound =  document.createElement('div')
      notfound.className = 'payoo-bank-not-found';
      let notfoundImg = document.createElement('img')
      notfoundImg.className = 'payoo-bank-not-found-img';
      let notfoundText = document.createElement('div')
      notfoundText.className = 'payoo-bank-not-found-text';
      notfoundImg.src = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODYiIGhlaWdodD0iMTA0IiB2aWV3Qm94PSIwIDAgODYgMTA0IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBkPSJNMS44Mjg2MSAySDU5LjIwNTdMODQuMTcxMyAyNy4xMTE2VjEwMkgxLjgyODYxVjJaIiBmaWxsPSJ3aGl0ZSIgc3Ryb2tlPSIjRTBFMEUwIiBzdHJva2Utd2lkdGg9IjMiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLWRhc2hhcnJheT0iMiA1Ii8+Cjwvc3ZnPgo="
      notfoundText.innerText = 'Không tìm thấy kết quả'
      notfound.style.display = 'none'
      search.placeholder = "Nhập tên ngân hàng cần tìm";
      searchDiv.append(icon)
      searchDiv.append(search)
      
      notfound.append(notfoundImg)
      notfound.append(notfoundText)
   
      search.addEventListener('input', this.onSearch)

      this.prepend(notfound);
      this.prepend(searchDiv);
    }

    onSearch(e)  {
      const icons = document.querySelectorAll('.payoo-option .bank-icon');
      let found = false
      icons.forEach(icon => {
        const code = icon.getAttribute('data-code').toLowerCase()
        const name = icon.getAttribute('data-name').toLowerCase()
  
        const val = e.target.value.toLowerCase()
        if ((!code.includes(val) && !name.includes(val)) && val != '') {
          icon.style.display = 'none'
        } else {
          icon.style.display = 'inline-grid'
          found = true
        }
      });

      const notfound =  document.querySelector('payoo-bank .payoo-bank-not-found')
      notfound.style.display = found ? 'none' : 'flex';
    }
  }

  window.customElements.define("payoo-bank", PayooBank);
