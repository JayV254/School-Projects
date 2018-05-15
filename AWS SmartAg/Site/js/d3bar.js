

var data = [10,20,30,40]

d3.select(".d3bar")
	.selectAll("div")
	.data(data)
		.enter()
		.append("div")
		.style("width", function(d){return d + 'px'})
		.text(function(d){return '$' + d;})

