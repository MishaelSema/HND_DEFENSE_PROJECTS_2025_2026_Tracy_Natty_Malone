# CHAPTER 3

# SYSTEM DESIGN METHODOLOGY

## 3.1 Introduction

This chapter talks about how I went about designing and building the Allo Service platform. I wanted to make something that actually helps people in Cameroon find and book local service providers like plumbers, electricians, cleaners, and others. So I had to first understand what people really need and then figure out the best way to build it.

I used a structured method but also stayed flexible enough to make changes when I learned something new. The platform is basically a website where clients can look for service providers, book their services, and leave reviews. People can also rate the services they get. This helps other people know who is good and who is not so good.

The whole process started with me trying to understand the problem. What do people struggle with when they need to find a worker? What do workers need to get more customers? These are the questions I asked myself and tried to answer through my research.

## 3.2 Data and Requirement Gathering

Before I could start building anything, I had to know what the system should actually do. This is what I call gathering requirements. It is important because if you build something without knowing what people want, you will waste time and money.

For this project, I looked at two main groups of people. First are the clients, that is normal people who need services like fixing a leak in their house or painting their office. Second are the providers, these are the skilled workers who offer services to clients. I also thought about the admin who would managed the whole system.

I gathered information from different places. Some came from reading about other similar platforms that work in other countries. Some came from talking directly to people in Cameroon. I also looked at how people currently find and hire service providers in the local market.

The requirements I collected were divided into two types. Functional requirements are the things the system must do, like allowing someone to search for a plumber or let a provider create their profile. Non-functional requirements are about how well the system should work, like being fast, being secure, and being easy to use on a phone.

## 3.3 Data Collection

I collected data using three main ways. First was surveys, second was interviews, and third was just watching and observing how things work in real life.

For the surveys, I made a questionnaire with both multiple choice questions and spaces where people could write their own answers. I shared this with people in Douala and Yaoundé mainly. I got responses from homeowners, business owners, and also from some service providers themselves. The questions asked about how they currently find workers, what problems they face, and what features they would like to see in a new platform.

The interviews were more personal. I sat down with a smaller group of people and talked to them in depth. This helped me understand things that surveys might miss. Like, I learned that many peoples are worried about paying money online to someone they do not know. This is a big deal and something I had to address in my design.

I also spent time just watching how people find services in the market. I went to different areas and observed the interactions between people looking for workers and workers trying to get jobs. This showed me that a lot of business still happens through phone calls and WhatsApp messages. So I made sure my platform can work well alongside these existing methods.

## 3.4 Quantitative and Qualitative Analysis

After collecting all the data, I had to analyze it to find useful information. I used both numbers and words to understand what I heard.

For the numbers part, I counted and calculated things from the survey. I found that about 75% of people said they really struggle to find good and reliable service providers when they need them. Almost 80% said they would love to use a phone app or website to find and book services. When I asked about what services they need most, plumbing came first, then electrical work, then cleaning. These numbers helped me decide what features to focus on first.

I also looked at who answered my questions. Age seemed to matter. Younger people were more excited about using a digital platform. But even older people could see the benefits, they just needed things to be simple and trustworthy.

For the words part, I read through what people wrote and said in interviews. One big theme that came up was TRUST. Many people were skeptical about using a platform to find workers because they were afraid of being scammed or getting poor quality work. Another theme was FLEXIBILITY. People did not want rigid booking forms, they wanted to be able to talk to the worker directly and agree on things like price and time.

By mixing both the numbers and the stories, I got a clear picture of what I need to build. This system has to help peoples trust the providers and it has to give them freedom to arrange things the way they want.

## 3.5 Processes

The way I built the system followed what is called the Software Development Life Cycle, or SDLC for short. This is basically a step by step plan that helps make sure I do things in the right order and do not forget important stuff.

First was the planning step. Here I decided what the project will include, how long it will take, and what I need to finish it. I also thought about what could go wrong and made plans for those situations.

Next was the analysis step. This is where I took all my research and turned it into a clear list of what the system must do. I wrote down every feature and requirement so that I could check against it later.

Then came the design step. Here I drew diagrams and made plans for how the system will work. I decided how the databse will be organized, how different pages will connect, and what buttons and forms will look like. This step is like drawing the blueprint before you start building a house.

After that was the building step, which I call implementation. This is where I actually wrote the code and put everything together. I did this in small pieces, testing each piece before moving to the next one.

Finally was the testing step. I tried to use the system myself and also asked other people to try it. I looked for mistakes and problems. When I found something wrong, I fixed it before moving on.

## 3.6 Methods and Techniques

For the way I worked, I chose something called agile methodology. This means I did not try to plan everything perfectly from the start. Instead, I worked in short rounds called sprints. Each sprint lasted a couple weeks and I tried to finish a certain piece of functionality in that time.

At the end of each sprint, I looked at what I made and asked myself if it was working well. If something was not right, I changed it before starting the next sprint. This flexible approach was good because my understanding of what users need changed as I went along.

### 3.6.1 How I Applied Agile

In practice, agile meant I had meetings every day briefly to talk about what I did yesterday, what I will do today, and if I have any problems. I also had bigger meetings at the end of each sprint to show my work and get feedback.

One example of how this helped was when I was working on the booking system. My first design was too complicated and users did not understand it. After the first sprint demo, I got feedback and realized it needed to be simpler. I changed it in the next sprint and it got much better. This kind of quick adjustment is what makes agile good.

### 3.6.2 Tools and Technologies I Used

For the actual building, I used some standard web technologies. The front part that users see was built with PHP, HTML, CSS, and some JavaScript. I chose these because they are widely used and there are many people in Cameroon who know how to work with them. Also these technologies work well on mobile phones which is how most peoples access the internet here.

The backend, that is the part that handles data and business logic, was also done with PHP. I made sure to follow security best practices to protect user information.

For storing data, I used MySQL which is a popular database system. It is reliable and can handle a lot of information without getting slow. I designed my database tables carefully to make sure everything is stored properly and can be found quickly when needed.

For development, I used WAMP which is a program that lets me run a local web server on Windows computers. This made it easy to test my work before putting it on a real server that everyone can access.

### 3.6.3 How I Designed the System Structure

I organized the system in three main parts. First is what the user sees and interacts with, I call this the presentation layer. Second is the part that does the thinking and processing, I call it the business logic layer. Third is where I keep all the information, this is the data layer.

This separation is good because it makes things neater and easier to fix if something breaks. If I want to change how the website looks, I only touch the presentation layer. If I need to change how I calculate prices, I only touch the business logic layer. Each part can be worked on separately.

### 3.6.4 How I Designed the Database

The database is where I keep all the important information. I designed it like a set of tables that connect to each other.

The most important table is for users. This includes everyone who uses the system, whether they are clients, providers, or admins. I use a role field to tell them apart. There are other tables for services, for bookings, for reviews, and for messages.

I used these connections using what are called foreign key which keep everything organized properly.

### 3.6.5 How I Tested Everything

Testing was not something I did only at the end. I did testing all through the development process. This helped me catch problems early before they became bigger issues.

I tested each small part of the code by itself first. Then I tested how different parts work together. Finally I tested the whole system from start to finish to make sure everything works as expected.

I also asked some real people to try using the system and tell me what they thought. This user testing gave me valuable feedback about what was confusing or difficult. I used this feedback to make the system better.

## 3.7 Conclusion

To wrap up this chapter, the methodology I used for Allo Service combines careful research with flexible development. I spent good time understanding what real people in Cameroon need from a service booking platform. This understanding came from surveys, interviews, and direct observation of how the market works today.

The findings showed that trust and flexibility are very important to users. People want to know they can trust the provider they hire, and they want the freedom to arrange things their own way. These insights guided my design decisions throughout the project.

The agile approach let me stay responsive to changing understanding while keeping the project on track. The tools and technologies I chose are appropriate for the local context and can be maintained without needing specialized expertise.

With this solid foundation of research and design, I move forward to Chapter 4 which will talk about how I actually built the system and what I learned from the process.