import {shallowMount, mount, createLocalVue} from '@vue/test-utils';
import VueRouter from 'vue-router'
import BootstrapVue from 'bootstrap-vue'
import Message from '@/views/apps/email/Message'

const localVue = createLocalVue()
localVue.use(VueRouter)
const router = new VueRouter()

localVue.use(BootstrapVue)

describe('Message.vue', () => {
  it('has a name', () => {
    expect(Message.name).toMatch('message')
  })
  it('is Vue instance', () => {
    const wrapper = shallowMount(Message, {
      localVue,
      router
    })
    expect(wrapper.isVueInstance()).toBe(true)
  })
  it('is Message', () => {
    const wrapper = shallowMount(Message, {
      localVue,
      router
    })
    expect(wrapper.is(Message)).toBe(true)
  })
  it('should render correct content', () => {
    const wrapper = mount(Message, {
      localVue,
      router
    })
    expect(wrapper.find('div.email-app > nav > .btn').text()).toMatch('New Email')
  })
  test('renders correctly', () => {
    const wrapper = shallowMount(Message, {
      localVue,
      router
    })
    expect(wrapper.element).toMatchSnapshot()
  })
})
