import React, { Component } from 'react';
import { AppRegistry, StyleSheet, Text, View, Image, Button, Alert} from 'react-native';

const cool = 'You are cool!';
const awesome = 'You are awesome!';


export default class LotsOfStyles extends Component {


   constructor(props) {
    super(props);

    this.state = {name: cool};
    
    this.handleEvent = this.handleEvent.bind(this);
  }

  /*changeAlert() {
    const newAlert = this.state.name == cool ? awesome : cool;
    this.setState({name: newAlert})
    
  }*/

  handleEvent() {
    
    const newAlert = this.state.name == cool ? awesome : cool;
    this.setState({name: newAlert});
    Alert.alert(this.state.name)
  }


  render() {
    

    return (
      <View style={styles.container}>
       
        <View style={styles.box1}>
          <Text style={styles.bigpurple}>Welcome</Text>
          <Text style={styles.red}>Hi, it's Syfa</Text>
        </View>
       
        <View style={styles.box2}>  
          <Image
          style={{ flex:1, width: 500, alignSelf:'center'}} resizeMode="stretch"
          source={{uri: 'https://upload.wikimedia.org/wikipedia/commons/b/b2/Endangered_Red_Panda.jpg'}}
          />
        </View>  
        
        <View style={styles.box3}>
          <Text style={styles.red}>Click this button again and again for a 50% chance of getting a different result:</Text>
          
          <View style={styles.buttonContainer}>
          <Button onPress={this.handleEvent}  title="Click Me" />
          </View>
        
        </View>
      </View>
      
    );
  }
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    flexDirection: 'column',
    
  },
  box1: {
    flex:0.8,
    backgroundColor: '#2196F3',
    justifyContent: 'center',
  },
  box2: {
    flex:2,
    backgroundColor: '#8BC34A',
    justifyContent: 'center',
  },
  box3: {
    flex: 1,
    backgroundColor: '#e3aa1a',
    justifyContent: 'center',
  },
  bigpurple: {
    color: 'purple',
    fontWeight: 'bold',
    fontSize: 30,
    textAlign: 'center',
    marginBottom: 10,
    
  },
  red: {
    color: 'white',
    textAlign: 'center',
   
  },
  buttonContainer: {
    backgroundColor: '#2E9298',
    borderRadius: 10,
    padding: 10,
    width: 300,
    marginTop:20,
    alignSelf: 'center',
    shadowColor: '#000000',
    shadowOffset: {
      width: 0,
      height: 3
    },
    shadowRadius: 10,
    shadowOpacity: 0.25
  }
});


AppRegistry.registerComponent('AwesomeProject', () => LotsOfStyles);