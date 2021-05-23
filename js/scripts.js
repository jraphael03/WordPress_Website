import "../css/style.css"

// Our modules / classes
import MobileMenu from "./modules/MobileMenu"   // Converts menu to burger menu
import HeroSlider from "./modules/HeroSlider"   // Slide show feature towards bottom of homepage
import Search from "./modules/Search";           // Search function
import MyNotes from "./modules/MyNotes"
import Like from "./modules/Like"

// import GoogleMap from "./modules/GoogleMap"     // JS for google map


// Instantiate a new object using our modules/classes
var mobileMenu = new MobileMenu()
var heroSlider = new HeroSlider()
var LiveSearch = new Search()
var mynotes = new MyNotes()   // Created new object
var like = new Like()

// var googleMap = new GoogleMap()

// Allow new JS and CSS to load in browser without a traditional page refresh
if (module.hot) {
  module.hot.accept()
}
