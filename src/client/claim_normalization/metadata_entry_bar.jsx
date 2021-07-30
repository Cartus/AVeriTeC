const Tooltip = ({ children, text, ...rest }) => {
    const [show, setShow] = React.useState(false);
  
    return (
      <div className="tooltip-container">
        <div className={show ? 'tooltip-box visible' : 'tooltip-box'}>
          {text}
          <span className="tooltip-arrow" />
        </div>
        <div
          onMouseEnter={() => setShow(true)}
          onMouseLeave={() => setShow(false)}
          {...rest}
        >
          {children}
        </div>
      </div>
    );
  };


class ClaimEntryField extends React.Component {
    constructor(props) {
        super(props);

        this.handleFieldChange = this.handleFieldChange.bind(this);
    }

    handleFieldChange = event => {
        const { name, value } = event.target;
        this.props.onChange(this.props.id, name, value);
    }

    render() {
        return (
            <div>
                <span  id='claim-hyperlink' className="box-label">Hyperlink:</span>
                <input type="text"  className='hyperlink-claim'  onChange={this.handleFieldChange} name='hyperlink'></input>

                <Tooltip text="A hyperlink to the original claim, if that is provided by the fact checking site. Examples of this include Facebook posts, the original article or blog post being fact checked, and embedded video links. If the original claim has a hyperlink on the fact checking site, but that hyperlink is dead, annotators should leave the field empty.">
                    <button type="button" className="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info"></button>
                </Tooltip>

                <select name="fruit_selector" onChange={this.handleFieldChange}>
                <option value="grapefruit">Grapefruit</option>
                <option value="lime">Lime</option>
                <option selected value="coconut">Coconut</option>
                <option value="mango">Mango</option>
                </select>


            </div>        
        );
      }
}

class ClaimEntryContainer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            claimEntries: {
                "claim_entry_field_0": this.new_claim_dict()
            }
        };
        this.handleFieldChange = this.handleFieldChange.bind(this);
      }

    new_claim_dict = () => {
        return {};
    }

    addClaim = () => {
        const numClaims = Object.keys(this.state.claimEntries).length;
        const field_id = "claim_entry_field_" + numClaims
        
        this.setState({
            claimEntries: {
                ...this.state.claimEntries, 
                [field_id]:this.new_claim_dict()
            }
        });
    }

    handleFieldChange(fieldId, element, value) {
        this.setState(prevState => ({
            claimEntries: {
                ...prevState.claimEntries,
                [fieldId]: {
                    ...prevState.claimEntries[fieldId],
                    [element]: value
                }
            }
        }))
      }

    render() {
        const claimEntryFields = Object.keys(this.state.claimEntries).map(field_id => (
            <ClaimEntryField
              key={field_id}
              id={field_id}
              onChange={this.handleFieldChange}
            />
          ));
        
        return (
            <div>
                {claimEntryFields}
                <button onClick={this.addClaim}>Add Claim</button>
                <button onClick={this.doSubmit}>Submit</button>
                <div>{JSON.stringify(this.state)}</div>
            </div>
        );
      }
}

let domContainer = document.querySelector('#claim_entry_container');
ReactDOM.render(<ClaimEntryContainer />, domContainer);