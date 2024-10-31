class PTC_CompareTable {

	constructor() {
		this.products = []
		this.tableProducts = []
		this.index = 0
		this.columnLimit = this.showLimit()
		this.filterArray = []

	}


	getProducts(result) {
		const res = JSON.parse(result)
		console.log(res)
		this.products = res.all_products
		this.tableProducts = res.all_products
		this.drawTable()
	}



	drawTable() {
		const { tableProducts, drawRightArraow, columnLimit, index, getCellAttrValues, filterArray, getColorHexes, drawRightColumn } = this
		const theTableBody = document.getElementById('ptc-tbody')
		const theTableHead = document.getElementById('ptc-thead')


		const prodLimit = columnLimit > tableProducts.length ? tableProducts.length : columnLimit
		this.drawLeftColumn(theTableHead, theTableBody)
		for (let i = 0; i < prodLimit; i++) {
			const prodIndex = i + index
			const tableHeadRow = document.getElementById('ptc-table-head-row')
			const headCellDiv = document.createElement('td');
			if (filterArray.findIndex(val => tableProducts[prodIndex].index === val) > -1) {
				headCellDiv.innerHTML = `<img src="${tableProducts[prodIndex].image}"><h3>${tableProducts[prodIndex].name}</h3><input type="checkbox"  checked onclick="table.handleIndexArray(${tableProducts[prodIndex].index})">`
			} else {
				headCellDiv.innerHTML = `<img src="${tableProducts[prodIndex].image}"><h3>${tableProducts[prodIndex].name}</h3><input type="checkbox" onclick="table.handleIndexArray(${tableProducts[prodIndex].index})"/><span></span>`
			}
			tableHeadRow.appendChild(headCellDiv)
			if (tableProducts[prodIndex].atributes) {
				tableProducts[prodIndex].atributes.forEach(row => {
					const currentAttrRow = document.getElementById(`cellRow-${row.cell_name}`)
					const bodyCellDiv = document.createElement('td');
					if (row.cell_data[0] && row.cell_data[0].hex) {
						bodyCellDiv.innerHTML = getColorHexes(row.cell_data)
					} else {
						bodyCellDiv.innerHTML = `<p>${getCellAttrValues(row.cell_data)}</p>`
					}
					currentAttrRow.appendChild(bodyCellDiv)

				})
			}
			if (tableProducts[prodIndex].info) {
				Object.entries(tableProducts[prodIndex].info).forEach(([key, value]) => {
					const currentInfoRow = document.getElementById(`cellRow-${key}`)
					const bodyCellDiv = document.createElement('td');
					bodyCellDiv.innerHTML = `<p>${value}</p>`
					currentInfoRow.appendChild(bodyCellDiv)

				})
			}
			if (tableProducts[prodIndex].acfs) {
				Object.entries(tableProducts[prodIndex].acfs).forEach(([key, value]) => {
					const currentInfoRow = document.getElementById(`cellRow-${key}`)
					const bodyCellDiv = document.createElement('td');
					if (!value) {
						bodyCellDiv.innerHTML = `<p></p>`
					} else if (value.toLowerCase() === 'yes') {
						bodyCellDiv.innerHTML = `<p><i aria-hidden="true" class="fas fa-check"></i></p>`
					} else {
						bodyCellDiv.innerHTML = `<p>${value}</p>`
					}

					currentInfoRow.appendChild(bodyCellDiv)

				})
			}
		}

		drawRightArraow(index, tableProducts, columnLimit)
		drawRightColumn()
	}


	drawLeftColumn(theTableHead, theTableBody) {
		const { products, index } = this
		if (!index) {
			theTableHead.innerHTML = '<tr id="ptc-table-head-row"><td><h1  class="slider-disable" onclick="table.changeIndex(-1)"><</h1></td></tr>'
		} else {
			theTableHead.innerHTML = '<tr id="ptc-table-head-row"><td><h1 onclick="table.changeIndex(-1)"><</h1></td></tr>'
		}
		theTableBody.innerHTML = ''
		if (products[0].info) {
			Object.keys(products[0].info).forEach(row => {
				const tableCellLeft = document.createElement('tr')
				tableCellLeft.setAttribute("id", `cellRow-${row}`);
				tableCellLeft.innerHTML = `<td><h5>${row}</h5></td>`
				theTableBody.appendChild(tableCellLeft)
			})
		}
		if (products[0].atributes) {
			products[0].atributes.forEach(row => {
				const tableCellLeft = document.createElement('tr')
				tableCellLeft.setAttribute("id", `cellRow-${row.cell_name}`);
				tableCellLeft.innerHTML = `<td><h5>${row.cell_name}</h5></td>`
				theTableBody.appendChild(tableCellLeft)
			})
		}
		if (products[0].acfs) {
			Object.keys(products[0].acfs).forEach(row => {
				const tableCellLeft = document.createElement('tr')
				tableCellLeft.setAttribute("id", `cellRow-${row}`);
				tableCellLeft.innerHTML = `<td><h5>${row}</h5></td>`
				theTableBody.appendChild(tableCellLeft)
			})
		}

	}


	drawRightArraow(index, tableProducts, columnLimit) {
		const tableHeadRow = document.getElementById('ptc-table-head-row')
		const headCellDiv = document.createElement('td')
		if (index + columnLimit >= tableProducts.length) {
			headCellDiv.innerHTML = '<h1 class="slider-disable" onclick="table.changeIndex(1)">></h1>'
		} else {
			headCellDiv.innerHTML = '<h1 onclick="table.changeIndex(1)">></h1>'
		}
		tableHeadRow.appendChild(headCellDiv)
	}

	drawRightColumn() {
		const children = [].slice.call(document.getElementById('ptc-tbody').children);
		children.forEach(row => {
			const lastCellDiv = document.createElement('td')
			lastCellDiv.innerHTML = '<p></p>'
			row.appendChild(lastCellDiv)
		})
	}


	filterTable() {
		const { products, filterArray } = this
		const newArray = products.filter(item => {
			return filterArray.includes(item.index)
		})

		if (!newArray.length) return
		this.tableProducts = [...newArray]
		this.index = 0
		this.filterArray = []
		this.drawTable()
	}

	resetTable() {
		const { products } = this
		this.tableProducts = [...products]
		this.filterArray = []
		this.index = 0
		this.drawTable()
	}


	showLimit() {
		if (screen.width > 900) {
			return 4
		} else if (screen.width > 900) {
			return 3
		}
		else return 1
	}


	changeIndex(val) {
		const { index, columnLimit, tableProducts } = this
		if (val < 0) {
			if (!index) return
			this.index -= 1
		} else {
			if ((index + columnLimit) >= tableProducts.length) return
			this.index += 1
		}
		this.drawTable()

	}


	getCellAttrValues(data) {
		if (!data || !data[0]) return ''
		const resArray = data.map(val => val.name)
		return resArray.join(", ")
	}

	getColorHexes(data) {
		if (!data || !data[0]) return ''
		const hexStyle = 'height: 20px; width: 20px; display: inline-block; margin: 2px;  border-radius: 100%; '
		const resArray = data.reduce((res, item) => {
			return res += `<object><div  class="variable-item-span variable-item-span-color" style="${hexStyle} background-color: ${item.hex[0]}"></div></object>`
		}, "")
		return `<aside class="available-colour-options">${resArray}</aside>`
	}



	handleIndexArray(val) {
		const { filterArray } = this
		if (filterArray.findIndex(item => item === val) > -1) return this.filterArray = filterArray.filter(item => item !== val)
		this.filterArray.push(val)
	}

}



