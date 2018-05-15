//setwd(“<SET YOUR WORKING DIRECTORY>”)

Pref = read.csv("Preference.csv")
Attr = read.csv("Edited_Attribute.csv")
Pref=Pref[,-1]
str(Attr)
str(Pref)
m1 = max(Pref)

# We want to reverse the ratings so that smaller score indicates higher preference
# Therefore, the input for preference data is dissimilarity
rating = m1+1 - Pref

# Smacof library does not take negative numbers as input
rating = scale(rating,center=FALSE,scale=TRUE)
install.packages("smacof")
library(smacof)

# Let's start from 1 dimensional solution first and increase the number of dimensions
fit.phone1 = unfolding(rating, ndim = 1, circle="none",weightmat = NULL, init = NULL, itmax = 10000, eps = 1e-6) 
fit.phone2 = unfolding(rating, ndim = 2, circle="none",weightmat = NULL, init = NULL, itmax = 10000, eps = 1e-6) 
fit.phone3 = unfolding(rating, ndim = 3, circle="none",weightmat = NULL, init = NULL, itmax = 10000, eps = 1e-6) 
fit.phone4 = unfolding(rating, ndim = 4, circle="none",weightmat = NULL, init = NULL, itmax = 10000, eps = 1e-6) 
fit.phone5 = unfolding(rating, ndim = 5, circle="none",weightmat = NULL, init = NULL, itmax = 10000, eps = 1e-6) 

# Unfolding model does not provide RSS
stress_phones = cbind(c(fit.phone1$stress, fit.phone2$stress,fit.phone3$stress,fit.phone4$stress,fit.phone5$stress))
plot(stress_phones,type = "b")

# We select 3 dimensional solution as well
plot(fit.phone3, plot.type = "confplot", plot.dim = c(1,2),label.conf.rows = list(label=FALSE), cex=1)
plot(fit.phone3, plot.type = "confplot", plot.dim = c(2,3),label.conf.rows = list(label=FALSE), cex=2)
plot(fit.phone3, plot.type = "confplot", plot.dim = c(1,3),label.conf.rows = list(label=FALSE), cex=2)

# brand coordinates
fit.phone3$conf.col

# respondent's ideal points
fit.phone3$conf.row

##########################################
# Regression with Attribute data
##########################################
# We want to regress attribute information on coordinates by dimension
# To facilitate interpretation, we want to draw a vector
##########################################
Attr1 = as.data.frame(Attr)
brand_names = Attr[,1]
Attr1 = Attr[,-1]
Attr1 = scale(Attr1,center=TRUE,scale=TRUE)
row.names(Attr1) = brand_names
Attr1 = as.data.frame(Attr1)


dim = 3
n = ncol(Attr1)

my_coefs1 = sapply(1:n, function(x) coef(lm(fit.phone3$conf.col[,1] ~ Attr1[,x])))
my_coefs2 = sapply(1:n, function(x) coef(lm(fit.phone3$conf.col[,2] ~ Attr1[,x])))
my_coefs3 = sapply(1:n, function(x) coef(lm(fit.phone3$conf.col[,3] ~ Attr1[,x])))
my_coeff = rbind(my_coefs1[2,],my_coefs2[2,],my_coefs3[2,])


#Since the vector size depends on actual coordinates, we need to multiply a constant for better interpretation
constant1 = 8 # I'm multiplying this constant for better interpretation. 
mycoeff = as.data.frame(t(my_coeff)*constant1)
rownames(mycoeff) = colnames(Attr1)
colnames(mycoeff)=cbind("D1","D2","D3")

fit.phone3$conf.col = rbind(fit.phone3$conf.col,mycoeff)
# Dimension 1 vs. 2
plot(fit.phone3, plot.type = "confplot", plot.dim = c(1,2),label.conf.rows = list(label=FALSE),cex=2)


for (j in 1:n) {
  arrows(0,0,mycoeff[j,1],mycoeff[j,2], length = .05)
}
# Dimension 2 vs. 3
plot(fit.phone3, plot.type = "confplot", plot.dim = c(2,3),label.conf.rows = list(label=FALSE),cex=2)

for (j in 1:n) {
  arrows(0,0,mycoeff[j,2],mycoeff[j,3], length = .05)
}

# Dimension 1 vs. 3
plot(fit.phone3, plot.type = "confplot", plot.dim = c(1,3),label.conf.rows = list(label=FALSE),cex=2)

for (j in 1:n) {
  arrows(0,0,mycoeff[j,1],mycoeff[j,3], length = .05)
}

#########################################################################################
# Now Let's run a cluster analysis                                                      #
# The Purpose is to exhibit different segments on Joint Perceptual Map                  #
#########################################################################################
# brand coordinates
fit.phone3$conf.col
write.table(fit.phone3$conf.col, "clipboard", sep="\t")
# respondent's ideal points
fit.phone3$conf.row

# step 1: we run mclust to identify the number of segments
library("mclust")
# We Run mclust with customers' ideal points
members.mclust = Mclust(fit.phone3$conf.row)
members.mclust$BIC
summary(members.mclust, parameters = TRUE)
d1 = aggregate(Pref, by = list(members.mclust$classification), mean)
d1
d2 = aggregate(fit.phone3$conf.row, by = list(members.mclust$classification), mean)
d2
write.table(d1, "clipboard", sep="\t")
#plot(members.mclust)
member1 = members.mclust$classification
member2 = factor(member1)

fit.phone3$conf.row = cbind(fit.phone3$conf.row,member2)


dim_change = matrix(c(1,2,1,2,3,3),nrow=3, ncol = 2)
dim_change
for (k1 in 1:nrow(dim_change)) {
  for (k2 in 1:ncol(dim_change)) {
    
    if (k1 != k2) {
      plot(fit.phone3, plot.type = "confplot", plot.dim = c(k2,k1),label.conf.rows = list(label=FALSE),cex = 2,
           label.conf.column = list(label=TRUE, pos = 3, cex = 1), col.rows=c("red","blue","green","yellow","black")[member2])
      
      for (j in 1:n) {
        arrows(0,0,mycoeff[j,k2],mycoeff[j,k1], length = .05)
      }
    }
  }
}

